<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationItem extends Model
{
    use HasFactory;

protected $fillable = [
    'donation_id',
    'store_item_id',
    'variant_id',
    'item',
    'quantity',
    'unit',
    'condition',
    'estimated_value',
    'notes',
];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_value' => 'decimal:2',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function storeItem()
{
    return $this->belongsTo(
       StoreItem::class,
        'store_item_id'
    );
}

public function variant()
{
    return $this->belongsTo(
       StoreItemVariant::class,
        'variant_id'
    );
}

}




