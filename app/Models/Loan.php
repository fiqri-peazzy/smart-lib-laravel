<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * @property \Carbon\Carbon $loan_date
 * @property \Carbon\Carbon $due_date
 * @property \Carbon\Carbon|null $return_date
 * @property \Carbon\Carbon|null $original_due_date
 * @property \Carbon\Carbon|null $extended_at
 * @property \Carbon\Carbon|null $fine_paid_at
 */
class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'book_item_id',
        'processed_by',
        'returned_to',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'is_extended',
        'original_due_date',
        'extended_at',
        'fine_amount',
        'fine_paid',
        'fine_paid_at',
        'return_condition',
        'return_notes',
        'notes',
        'requested_at',
        'pickup_deadline',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'original_due_date' => 'date',
        'extended_at' => 'datetime',
        'fine_paid_at' => 'datetime',
        'is_extended' => 'boolean',
        'fine_paid' => 'boolean',
        'fine_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'pickup_deadline' => 'datetime',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            // Only set loan_date for non-pending_pickup loans
            if (! $loan->loan_date && $loan->status !== 'pending_pickup') {
                $loan->loan_date = now();
            }

            // Update book item status
            if ($loan->book_item_id) {
                $bookItem = BookItem::find($loan->book_item_id);
                if ($bookItem) {
                    $bookItem->update(['status' => 'on_loan']);
                }
            }
        });

        static::created(function ($loan) {
            // Update loan history only for active loans
            if ($loan->status !== 'pending_pickup') {
                $loan->user->updateLoanHistory();
            }
        });

        static::updated(function ($loan) {
            // Check for overdue
            if ($loan->status === 'active' && $loan->isOverdue()) {
                $loan->updateQuietly(['status' => 'overdue']);
            }

            // Auto-cancel expired pending pickups
            if ($loan->status === 'pending_pickup' && $loan->isPickupExpired()) {
                $loan->cancelPendingPickup();
            }
        });
    }

    /**
     * Relasi ke user (peminjam)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke bookItem
     */
    public function bookItem(): BelongsTo
    {
        return $this->belongsTo(BookItem::class);
    }

    /**
     * Relasi ke user yang memproses
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Relasi ke user yang menerima pengembalian
     */
    public function returnedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    /**
     * Relasi ke fine
     */
    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    /**
     * Scope active loans
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'overdue', 'extended']);
    }

    /**
     * Scope pending pickup loans
     */
    public function scopePendingPickup($query)
    {
        return $query->where('status', 'pending_pickup');
    }

    /**
     * Scope overdue loans
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                    ->where('due_date', '<', now());
            });
    }

    /**
     * Check if loan is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->return_date) {
            return false;
        }
        if (! $this->due_date) {
            return false;
        }

        return now()->isAfter($this->due_date);
    }

    /**
     * Check if pending pickup is expired
     */
    public function isPickupExpired(): bool
    {
        if ($this->status !== 'pending_pickup') {
            return false;
        }

        if (! $this->pickup_deadline) {
            return false;
        }

        return now()->isAfter($this->pickup_deadline);
    }

    /**
     * Get days until pickup deadline
     */
    public function getDaysUntilPickupAttribute(): int
    {
        if ($this->status !== 'pending_pickup' || ! $this->pickup_deadline) {
            return 0;
        }

        return now()->diffInDays($this->pickup_deadline, false);
    }

    /**
     * Get days overdue (always positive)
     */
    public function getDaysOverdue(): int
    {
        $returnDate = $this->return_date;
        $dueDate = $this->due_date;

        if (! $dueDate) {
            return 0;
        }

        if (! $returnDate) {
            if (now()->isAfter($dueDate)) {
                return (int) abs(now()->diffInDays($dueDate, false));
            }

            return 0;
        }

        if ($returnDate->isAfter($dueDate)) {
            return (int) abs($returnDate->diffInDays($dueDate, false));
        }

        return 0;
    }

    /**
     * Calculate fine amount
     */
    public function calculateFine(): float
    {
        if ($this->user && $this->user->isDosen()) {
            return 0;
        }

        $daysOverdue = abs($this->getDaysOverdue());
        if ($daysOverdue <= 0) {
            return 0;
        }

        $finePerDay = 1000;
        $maxFine = 50000;
        $calculatedFine = $daysOverdue * $finePerDay;

        return (float) min($calculatedFine, $maxFine);
    }

    /**
     * Can be extended?
     */
    public function canBeExtended(): bool
    {
        if ($this->is_extended || ! $this->due_date) {
            return false;
        }

        if (! in_array($this->status, ['active', 'overdue'])) {
            return false;
        }

        if ($this->bookItem) {
            $hasPendingBookings = Booking::where('book_id', $this->bookItem->book_id)
                ->where('status', 'pending')
                ->exists();

            return ! $hasPendingBookings;
        }

        return true;
    }

    /**
     * Extend loan
     */
    public function extend(int $additionalDays = 7): bool
    {
        if (! $this->canBeExtended()) {
            return false;
        }

        $this->update([
            'is_extended' => true,
            'original_due_date' => $this->due_date,
            'due_date' => $this->due_date->addDays((int) $additionalDays),
            'extended_at' => now(),
            'status' => 'extended',
        ]);

        return true;
    }

    /**
     * Confirm pickup (staff action)
     */
    public function confirmPickup(int $processedBy, ?int $loanDays = 14): void
    {
        $this->update([
            'status' => 'active',
            'loan_date' => now(),
            'due_date' => $loanDays ? now()->addDays((int) $loanDays) : null,
            'processed_by' => $processedBy,
            'pickup_deadline' => null,
        ]);

        if ($this->bookItem && $this->status === 'active') {
            $this->bookItem->update(['status' => 'on_loan']);
        }

        // Update loan history
        $this->user->updateLoanHistory();
    }

    /**
     * Cancel pending pickup (auto or manual)
     */
    public function cancelPendingPickup(?string $reason = null): void
    {
        if ($this->status !== 'pending_pickup') {
            return;
        }

        // Return book item stock
        if ($this->bookItem) {
            $this->bookItem->update(['status' => 'available']);
        }

        // Soft delete the loan request
        $this->update([
            'notes' => $this->notes."\n".now()->format('Y-m-d H:i').': '.($reason ?? 'Pickup expired'),
        ]);

        $this->delete();
    }

    /**
     * Process return
     */
    public function processReturn(
        string $condition,
        ?string $notes = null,
        ?int $returnedTo = null
    ): void {
        $returnDate = now();
        $daysLate = 0;
        if ($this->due_date && $returnDate->isAfter($this->due_date)) {
            $daysLate = (int) abs($returnDate->diffInDays($this->due_date, false));
        }

        $fineAmount = 0;
        if ($this->user && $this->user->isDosen()) {
            $fineAmount = 0;
        } elseif ($daysLate > 0) {
            $finePerDay = 1000;
            $maxFine = 50000;
            $fineAmount = min($daysLate * $finePerDay, $maxFine);
        }

        // Hitung denda kerusakan otomatis
        $damageFineAmount = 0;
        if (in_array($condition, ['poor', 'damaged'])) {
            $damageFineAmount = 50000; // Contoh denda buku rusak
        } elseif ($condition === 'lost') {
            $damageFineAmount = 100000; // Contoh denda buku hilang
        }

        $totalFineAmount = $fineAmount + $damageFineAmount;

        $this->update([
            'return_date' => $returnDate,
            'status' => 'returned',
            'return_condition' => $condition,
            'return_notes' => $notes,
            'returned_to' => $returnedTo ?? Auth::id(),
            'fine_amount' => $totalFineAmount,
        ]);

        if ($totalFineAmount > 0) {
            Fine::create([
                'loan_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $totalFineAmount,
                'days_overdue' => $daysLate,
                'daily_rate' => 1000,
            ]);

            $this->user->increment('total_fines', $totalFineAmount);
        }

        if ($this->bookItem) {
            $itemStatus = 'available';
            if ($condition === 'lost') {
                $itemStatus = 'lost';
            } elseif ($condition === 'damaged' || $condition === 'poor') {
                $itemStatus = 'damaged';
            }

            $this->bookItem->update([
                'status' => $itemStatus,
                'condition' => $condition,
            ]);
        }

        $this->user->updateLoanHistory();
        $this->user->recalculateCreditScore();
        $this->user->updateMaxLoans();

        $this->notifyBookings();
    }

    /**
     * Notify users who booked this book
     */
    protected function notifyBookings(): void
    {
        if (! $this->bookItem || $this->bookItem->status !== 'available') {
            return;
        }

        // Cari 1 antrean terlama (dengan priority tertinggi di atas)
        $booking = Booking::where('book_id', $this->bookItem->book_id)
            ->where('status', 'pending')
            ->orderBy('is_priority', 'desc')
            ->orderBy('booking_date', 'asc')
            ->first();

        if ($booking) {
            // 1. Notify user (status -> notified, expires_at -> +24 jam)
            $booking->notify();

            // 2. Tahan eksemplar ini
            $this->bookItem->update(['status' => 'reserved']);

            // 3. Buat Loan pending_pickup untuk booking agar siap diambil
            self::create([
                'user_id' => $booking->user_id,
                'book_item_id' => $this->bookItem->id,
                'status' => 'pending_pickup',
                'requested_at' => now(),
                'pickup_deadline' => now()->addDay(), // 1x24 jam
                'loan_date' => null,
                'due_date' => null,
            ]);
        }
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending_pickup' => 'warning',
            'active' => 'success',
            'extended' => 'info',
            'overdue' => 'danger',
            'returned' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get days until due (negative if overdue)
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if ($this->return_date || ! $this->due_date) {
            return null; // or handle dynamically
        }

        return now()->diffInDays($this->due_date, false);
    }
}
