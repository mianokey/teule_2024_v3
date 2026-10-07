<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'unit_id',
        'name',
        'sku',
        'item_type',
        'description',
        'reorder_level',
        'is_active',
    ];

    protected $casts = [
        'reorder_level' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(StoreItemCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(StoreUnit::class, 'unit_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(StoreStock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StoreStockMovement::class);
    }

    public function variants(): HasMany
{
    return $this->hasMany(StoreItemVariant::class, 'store_item_id');
}

}