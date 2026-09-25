<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'transaction_type',
        'trans_time',
        'amount',
        'business_short_code',
        'bill_ref_number',
        'invoice_number',
        'phone_number',
        'first_name',
        'middle_name',
        'last_name',
        'org_account_balance',
        'third_party_trans_id',
        'donor_id',
        'donation_id',
        'classification',
        'status',
        'reviewed_by',
        'reviewed_at',
        'notes',
        'raw_payload',
        'received_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_payload' => 'array',
        'reviewed_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}