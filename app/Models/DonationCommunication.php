<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'donor_id',
        'sent_by',
        'channel',
        'type',
        'recipient',
        'subject',
        'message',
        'status',
        'provider_reference',
        'error_message',
        'scheduled_at',
        'sent_at',
        'cancelled_at',
        'delivered_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}