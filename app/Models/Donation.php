<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_number',
        'donor_id',
        'type',
        'source',
        'classification',
        'amount',
        'currency',
        'donation_date',
        'purpose',
        'reference',
        'payment_reference',
        'description',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'donation_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function items()
    {
        return $this->hasMany(DonationItem::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

public function communications()
{
    return $this->hasMany(DonationCommunication::class);
}



}