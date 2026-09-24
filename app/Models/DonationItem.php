<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'item',
        'quantity',
        'unit',
        'estimated_value',
        'condition',
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
}




