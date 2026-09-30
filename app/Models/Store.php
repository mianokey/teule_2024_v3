<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(StoreStock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StoreStockMovement::class);
    }

    public function sourceRequisitions(): HasMany
{
    return $this->hasMany(StoreRequisition::class, 'source_store_id');
}

public function destinationRequisitions(): HasMany
{
    return $this->hasMany(StoreRequisition::class, 'destination_store_id');
}


}