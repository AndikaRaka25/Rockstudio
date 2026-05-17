<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'images',
        'facilities',
        'price_per_session',
        'dp_amount',
        'session_duration_minutes',
        'operating_start',
        'operating_end',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'facilities' => 'array',
            'is_active' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * Get the 8 session templates for this studio.
     * Sesi 1: 08:00-10:00, Sesi 2: 10:00-12:00, ... Sesi 8: 22:00-24:00
     */
    public function getSessionTemplates(): array
    {
        $sessions = [];
        $startHour = (int) substr($this->operating_start, 0, 2);
        $durationHours = $this->session_duration_minutes / 60;

        for ($i = 1; $i <= 8; $i++) {
            $start = str_pad($startHour + (($i - 1) * $durationHours), 2, '0', STR_PAD_LEFT) . ':00';
            $end = str_pad($startHour + ($i * $durationHours), 2, '0', STR_PAD_LEFT) . ':00';
            $sessions[] = [
                'session_number' => $i,
                'start_time' => $start,
                'end_time' => $end,
            ];
        }

        return $sessions;
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Google Maps URL for redirect.
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps/place/Rockstar+Studio/@-7.7500701,110.4041983,1036m/data=!3m2!1e3!4b1!4m6!3m5!1s0x2e7a5976135decc7:0xedaa2ee50440320!8m2!3d-7.7500754!4d110.4067722!16s%2Fg%2F11hs3ym9c2";
    }
}
