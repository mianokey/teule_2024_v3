<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_receipt_id',
        'store_item_id',
        'variant_id',
        'store_lpo_item_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(StoreReceipt::class, 'store_receipt_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(StoreItemVariant::class, 'variant_id');
    }

public function lpoItem(): BelongsTo
{
    return $this->belongsTo(
        StoreLpoItem::class,
        'store_lpo_item_id'
    );
}

}