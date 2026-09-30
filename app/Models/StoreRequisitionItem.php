<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Child;

class StoreRequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_requisition_id',
        'store_item_id',
        'variant_id',
        'requested_quantity',
        'approved_quantity',
        'issued_quantity',
        'outstanding_quantity',
        'notes',
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:3',
        'approved_quantity' => 'decimal:3',
        'issued_quantity' => 'decimal:3',
        'outstanding_quantity' => 'decimal:3',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(
            StoreRequisition::class,
            'store_requisition_id'
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
    public function children()
{
    return $this->belongsToMany(
        Child::class,
        'store_requisition_item_child'
    )->withTimestamps();
}

public function fulfillments()
{
    return $this->hasMany(
        StoreFulfillmentItem::class,
        'store_requisition_item_id'
    );
}


}