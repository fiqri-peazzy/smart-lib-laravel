<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanHistory extends Model
{
    use HasFactory;

    protected $table = 'loan_history';

    protected $fillable = [
        'user_id',
        'total_loans',
        'on_time_returns',
        'late_returns',
        'total_extensions',
        'total_fines_incurred',
        'total_fines_paid',
        'active_loans',
        'overdue_loans',
        'calculated_score',
        'last_loan_at',
        'last_return_at',
    ];

    protected $casts = [
        'total_loans' => 'integer',
        'on_time_returns' => 'integer',
        'late_returns' => 'integer',
        'total_extensions' => 'integer',
        'active_loans' => 'integer',
        'overdue_loans' => 'integer',
        'total_fines_incurred' => 'decimal:2',
        'total_fines_paid' => 'decimal:2',
        'calculated_score' => 'decimal:2',
        'last_loan_at' => 'datetime',
        'last_return_at' => 'datetime',
    ];

    /**
     * Relasi ke user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get performance rating
     */
    public function getPerformanceRatingAttribute(): string
    {
        $score = $this->calculated_score;

        return match (true) {
            $score >= 90 => 'Excellent',
            $score >= 70 => 'Good',
            $score >= 50 => 'Fair',
            $score >= 30 => 'Poor',
            default => 'Critical',
        };
    }

    /**
     * Get on-time percentage
     */
    public function getOnTimePercentageAttribute(): float
    {
        if ($this->total_loans == 0) {
            return 100;
        }

        return ($this->on_time_returns / $this->total_loans) * 100;
    }

    /**
     * Get unpaid fines amount
     */
    public function getUnpaidFinesAttribute(): float
    {
        return $this->total_fines_incurred - $this->total_fines_paid;
    }
}
