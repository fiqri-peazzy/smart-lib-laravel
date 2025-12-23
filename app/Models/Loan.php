<?php

namespace App\Models;

use Carbon\Carbon;
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
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            // Auto-set loan_date if not set
            if (!$loan->loan_date) {
                $loan->loan_date = now();
            }

            // Update book item status
            $loan->bookItem->markAsBorrowed();
        });

        static::created(function ($loan) {
            // Update loan history
            $loan->user->updateLoanHistory();
        });

        static::updated(function ($loan) {
            // Check for overdue
            if ($loan->status === 'active' && $loan->isOverdue()) {
                $loan->updateQuietly(['status' => 'overdue']);
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
     * Relasi ke book item
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
            return false; // Already returned
        }

        return now()->isAfter($this->due_date);
    }

    /**
     * Get days overdue (always positive)
     */
    public function getDaysOverdue(): int
    {
        $returnDate = $this->return_date;
        $dueDate = $this->due_date;

        // Jika belum dikembalikan, hitung dari sekarang
        if (!$returnDate) {
            if (now()->isAfter($dueDate)) {
                return (int) abs(now()->diffInDays($dueDate, false));
            }
            return 0;
        }

        // Jika sudah dikembalikan, hitung dari return_date
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
        $daysOverdue = abs($this->getDaysOverdue());

        if ($daysOverdue <= 0) {
            return 0;
        }

        // Rp 1.000 per hari, max Rp 50.000
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
        // Already extended
        if ($this->is_extended) {
            return false;
        }

        // Not active
        if (!in_array($this->status, ['active', 'overdue'])) {
            return false;
        }

        // Check if book has pending bookings
        $hasPendingBookings = Booking::where('book_id', $this->bookItem->book_id)
            ->where('status', 'pending')
            ->exists();

        return !$hasPendingBookings;
    }

    /**
     * Extend loan
     */
    public function extend(int $additionalDays = 7): bool
    {
        if (!$this->canBeExtended()) {
            return false;
        }

        $this->update([
            'is_extended' => true,
            'original_due_date' => $this->due_date,
            'due_date' => $this->due_date->addDays($additionalDays),
            'extended_at' => now(),
            'status' => 'extended',
        ]);

        return true;
    }

    /**
     * Process return
     */
    public function processReturn(
        string $condition,
        ?string $notes = null,
        ?int $returnedTo = null
    ): void {
        // Calculate fine SEBELUM set return_date
        $returnDate = now();
        $daysLate = 0;

        if ($returnDate->isAfter($this->due_date)) {
            $daysLate = (int) abs($returnDate->diffInDays($this->due_date, false));
        }

        $fineAmount = 0;
        if ($daysLate > 0) {
            $finePerDay = 1000;
            $maxFine = 50000;
            $fineAmount = min($daysLate * $finePerDay, $maxFine);
        }

        // Update loan record
        $this->update([
            'return_date' => $returnDate,
            'status' => 'returned',
            'return_condition' => $condition,
            'return_notes' => $notes,
            'returned_to' => $returnedTo ?? Auth::id(),
            'fine_amount' => $fineAmount,
        ]);

        // Create fine record jika ada denda
        if ($fineAmount > 0) {
            Fine::create([
                'loan_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $fineAmount,
                'days_overdue' => $daysLate,
                'daily_rate' => 1000,
            ]);

            // Update total fines di user
            $this->user->increment('total_fines', $fineAmount);
        }

        // Update book item status
        $this->bookItem->update([
            'status' => 'available',
            'condition' => $condition,
        ]);

        // Update loan history
        $this->user->updateLoanHistory();

        // Recalculate credit score
        $this->user->recalculateCreditScore();
        $this->user->updateMaxLoans();

        // Notify bookings if any
        $this->notifyBookings();
    }

    /**
     * Notify users who booked this book
     */
    protected function notifyBookings(): void
    {
        $bookings = Booking::where('book_id', $this->bookItem->book_id)
            ->where('status', 'pending')
            ->orderBy('is_priority', 'desc')
            ->orderBy('booking_date', 'asc')
            ->get();

        foreach ($bookings as $booking) {
            $booking->notify();
        }
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
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
    public function getDaysUntilDueAttribute(): int
    {
        if ($this->return_date) {
            return 0;
        }

        return now()->diffInDays($this->due_date, false);
    }
}
