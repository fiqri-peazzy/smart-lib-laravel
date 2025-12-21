<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'booking_date',
        'expires_at',
        'status',
        'is_priority',
        'notified_at',
        'fulfilled_at',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'expires_at' => 'date',
        'notified_at' => 'datetime',
        'fulfilled_at' => 'datetime',
        'is_priority' => 'boolean',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            // Set booking date
            if (!$booking->booking_date) {
                $booking->booking_date = now();
            }

            // Set priority untuk dosen
            $user = User::find($booking->user_id);
            if ($user && $user->isDosen()) {
                $booking->is_priority = true;
            }

            // Set expired date (3 hari dari sekarang)
            if (!$booking->expires_at) {
                $booking->expires_at = now()->addDays(3);
            }
        });
    }

    /**
     * Relasi ke user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Scope pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope expired bookings
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '<', now());
    }

    /**
     * Check if expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'pending' &&
            now()->isAfter($this->expires_at);
    }

    /**
     * Notify user that book is available
     */
    public function notify(): void
    {
        $this->update([
            'status' => 'notified',
            'notified_at' => now(),
            'expires_at' => now()->addDays(3), // Reset expired 3 hari lagi
        ]);

        // TODO: Send actual notification (email/whatsapp)
        // Notification::send($this->user, new BookAvailableNotification($this));
    }

    /**
     * Mark as fulfilled
     */
    public function fulfill(): void
    {
        $this->update([
            'status' => 'fulfilled',
            'fulfilled_at' => now(),
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? $this->notes . "\nCancelled: " . $reason : $this->notes,
        ]);
    }

    /**
     * Auto-expire booking
     */
    public function expire(): void
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
            'pending' => 'warning',
            'notified' => 'info',
            'fulfilled' => 'success',
            'cancelled' => 'gray',
            'expired' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get days until expires
     */
    public function getDaysUntilExpiresAttribute(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        return now()->diffInDays($this->expires_at, false);
    }
}
