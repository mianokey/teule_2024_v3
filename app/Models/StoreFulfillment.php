<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreFulfillment extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_requisition_id',
        'transaction_number',
        'transaction_type',
        'source_store_id',
        'destination_store_id',
        'processed_by',
        'notes',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(
            StoreRequisition::class,
            'store_requisition_id'
        );
    }

    public function sourceStore(): BelongsTo
    {
        return $this->belongsTo(
            Store::class,
            'source_store_id'
        );
    }

    public function destinationStore(): BelongsTo
    {
        return $this->belongsTo(
            Store::class,
            'destination_store_id'
        );
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            StoreFulfillmentItem::class
        );
    }
}