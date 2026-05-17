<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Booking extends Model
{
    protected $fillable = [
        'studio_id',
        'user_id',
        'date',
        'session_number',
        'start_time',
        'end_time',
        'status',
        'band_name',
        'booker_name',
        'booker_phone',
        'notes',
        'payment_method',
        'midtrans_order_id',
        'snap_token',
        'amount',
        'dp_amount',
        'locked_at',
        'confirmed_at',
        'cancelled_at',
        'original_booking_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'locked_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // === Status Helpers ===

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Check if the 5-minute lock has expired.
     */
    public function isLockExpired(): bool
    {
        if (!$this->locked_at) {
            return true;
        }
        return $this->locked_at->addMinutes(5)->isPast();
    }

    /**
     * Check if cancellation is allowed (before H-1).
     * Cancellation allowed if booking date is more than 1 day away.
     */
    public function canCancel(): bool
    {
        if (!$this->isConfirmed()) {
            return false;
        }
        // H-1: booking date minus 1 day
        $deadline = $this->date->copy()->subDay()->startOfDay();
        return now()->lt($deadline);
    }

    /**
     * Check if reschedule is allowed (same rules as cancel: before H-1).
     */
    public function canReschedule(): bool
    {
        return $this->canCancel();
    }

    /**
     * Get remaining lock time in seconds.
     */
    public function getRemainingLockSecondsAttribute(): int
    {
        if (!$this->locked_at || !$this->isPending()) {
            return 0;
        }
        $remaining = $this->locked_at->addMinutes(5)->diffInSeconds(now(), false);
        return max(0, -$remaining);
    }

    // === Relationships ===

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function originalBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'original_booking_id');
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeForSchedule($query, int $studioId, string $startDate, string $endDate)
    {
        return $query->where('studio_id', $studioId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['pending', 'confirmed']);
    }
}
