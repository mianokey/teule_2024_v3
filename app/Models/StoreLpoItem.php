<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreLpoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_lpo_id',
        'store_item_id',
        'variant_id',
        'description',
        'ordered_quantity',
        'unit_price',
        'discount',
        'tax',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'ordered_quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function lpo(): BelongsTo
    {
        return $this->belongsTo(StoreLpo::class, 'store_lpo_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            StoreItemVariant::class,
            'variant_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Received Quantity
    |--------------------------------------------------------------------------
    |
    | This is calculated from actual Store Receipt Items.
    | We do not store received_quantity in the LPO table.
    |
    */
public function getReceivedQuantityAttribute(): float
{
    return (float) StoreReceiptItem::query()
        ->where('store_lpo_item_id', $this->id)
        ->whereHas('receipt', function ($query) {
            $query->where('status', 'POSTED');
        })
        ->sum('quantity');
}

public function getRemainingQuantityAttribute(): float
{
    return max(
        0,
        (float) $this->ordered_quantity - $this->received_quantity
    );
}

}

