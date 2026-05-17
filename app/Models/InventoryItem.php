<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
        'studio_id',
        'name',
        'category',
        'quantity',
        'condition',
        'purchase_date',
        'purchase_price',
        'notes',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
        ];
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    /**
     * Get localized category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'alat_musik' => __('inventory.category_musik'),
            'alat_rekaman' => __('inventory.category_rekaman'),
            'alat_elektronik' => __('inventory.category_elektronik'),
            default => $this->category,
        };
    }

    /**
     * Get condition badge color.
     */
    public function getConditionColorAttribute(): string
    {
        return match($this->condition) {
            'baik' => 'green',
            'cukup' => 'yellow',
            'perlu_perbaikan' => 'red',
            default => 'gray',
        };
    }
}
