<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'store_item_id',
        'quantity',
        'variant_id',
        'last_movement_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'last_movement_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }
    public function variant(): BelongsTo
{
    return $this->belongsTo(StoreItemVariant::class, 'variant_id');
}

}