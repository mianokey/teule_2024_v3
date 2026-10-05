<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreFulfillmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_fulfillment_id',
        'store_requisition_item_id',
        'store_item_id',
        'variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function fulfillment(): BelongsTo
    {
        return $this->belongsTo(
            StoreFulfillment::class,
            'store_fulfillment_id'
        );
    }

    public function requisitionItem(): BelongsTo
    {
        return $this->belongsTo(
            StoreRequisitionItem::class,
            'store_requisition_item_id'
        );
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            StoreItem::class,
            'store_item_id'
        );
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            StoreItemVariant::class,
            'variant_id'
        );
    }

   
    public function storeItem()
    {
        return $this->belongsTo(
            StoreItem::class,
            'store_item_id'
        );
    }

   
}