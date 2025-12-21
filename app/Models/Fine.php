<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'user_id',
        'amount',
        'days_overdue',
        'daily_rate',
        'status',
        'paid_amount',
        'paid_at',
        'paid_to',
        'is_waived',
        'waived_by',
        'waive_reason',
        'waived_at',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'date',
        'waived_at' => 'datetime',
        'is_waived' => 'boolean',
    ];

    /**
     * Relasi ke loan
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Relasi ke user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke user yang menerima pembayaran
     */
    public function paidTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_to');
    }

    /**
     * Relasi ke user yang waive denda
     */
    public function waivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
    }

    /**
     * Scope unpaid fines
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope paid fines
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Process payment
     */
    public function processPayment(
        float $amount,
        string $method = 'cash',
        ?string $reference = null,
        ?int $paidTo = null
    ): void {
        $this->update([
            'status' => 'paid',
            'paid_amount' => $amount,
            'paid_at' => now(),
            'paid_to' => $paidTo ?? Auth::id(),
            'payment_method' => $method,
            'payment_reference' => $reference,
        ]);

        // Update loan
        $this->loan->update([
            'fine_paid' => true,
            'fine_paid_at' => now(),
        ]);

        // Update user total fines
        $this->user->decrement('total_fines', $amount);

        // Update loan history
        $this->user->updateLoanHistory();

        // Recalculate credit score
        $this->user->recalculateCreditScore();
        $this->user->updateMaxLoans();
    }

    /**
     * Waive fine (admin only)
     */
    public function waive(string $reason, ?int $waivedBy = null): void
    {
        $this->update([
            'status' => 'waived',
            'is_waived' => true,
            'waived_by' => $waivedBy ?? Auth::id(),
            'waive_reason' => $reason,
            'waived_at' => now(),
        ]);

        // Update loan
        $this->loan->update([
            'fine_paid' => true,
            'fine_paid_at' => now(),
        ]);

        // Update user total fines
        $this->user->decrement('total_fines', $this->amount);

        // Update loan history
        $this->user->updateLoanHistory();

        // Recalculate credit score (waived doesn't penalize as much)
        $this->user->recalculateCreditScore();
        $this->user->updateMaxLoans();
    }

    /**
     * Check if fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->amount;
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->paid_amount);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'unpaid' => 'danger',
            'paid' => 'success',
            'waived' => 'info',
            default => 'gray',
        };
    }
}
