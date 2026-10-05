<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'store_id',
        'source_type',
        'supplier_name',
        'supplier_reference',
        'donation_id',
        'supplier_id',
        'store_lpo_id',
        'received_date',
        'received_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StoreReceiptItem::class);
    }

public function supplier(): BelongsTo
{
    return $this->belongsTo(
        Supplier::class,
        'supplier_id'
    );
}

public function lpo(): BelongsTo
{
    return $this->belongsTo(
        StoreLpo::class,
        'store_lpo_id'
    );
}



}