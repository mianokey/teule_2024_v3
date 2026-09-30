<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreItemVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_item_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(StoreStock::class, 'variant_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StoreStockMovement::class, 'variant_id');
    }
}