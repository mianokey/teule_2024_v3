<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StoreStockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'store_item_id',
        'variant_id',
        'movement_type',
        'quantity',
        'balance_after',
        'reference_type',
        'reference_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'balance_after' => 'decimal:3',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
    public function variant(): BelongsTo
{
    return $this->belongsTo(StoreItemVariant::class, 'variant_id');
}

}