<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_number',
        'name',
        'phone',
        'email',
        'organization',
        'address',
        'notes',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

public function communications()
{
    return $this->hasMany(DonationCommunication::class);
}
}