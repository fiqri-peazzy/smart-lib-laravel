<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'fine_id',
        'user_id',
        'amount',
        'payment_method',
        'payment_channel',
        'gateway_order_id',
        'gateway_transaction_id',
        'qr_code_url',
        'va_number',
        'status',
        'expires_at',
        'paid_at',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relasi ke fine
     */
    public function fine(): BelongsTo
    {
        return $this->belongsTo(Fine::class);
    }

    /**
     * Relasi ke user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope success transactions
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if transaction is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return now()->isAfter($this->expires_at);
    }

    /**
     * Mark as success
     */
    public function markAsSuccess(?string $transactionId = null): void
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
            'gateway_transaction_id' => $transactionId ?? $this->gateway_transaction_id,
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(?string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'notes' => $this->notes . "\nFailed: " . ($reason ?? 'Unknown reason'),
        ]);
    }

    /**
     * Mark as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'success' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'expired' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            'va' => 'Virtual Account',
            'ewallet' => 'E-Wallet',
            default => $this->payment_method,
        };
    }

    /**
     * Get formatted channel name
     */
    public function getChannelNameAttribute(): string
    {
        if (!$this->payment_channel) {
            return '-';
        }

        return match ($this->payment_channel) {
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'ovo' => 'OVO',
            'dana' => 'DANA',
            'linkaja' => 'LinkAja',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'mandiri_va' => 'Mandiri Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            default => strtoupper($this->payment_channel),
        };
    }
}