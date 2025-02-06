<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'payee_id',
        'payment_medium',
        'store_account',
        'amount',
        'purpose',
        'details',
        'status',
        'receipt',
    ];

    public function payee()
    {
        return $this->belongsTo(Payee::class);
    }

    public function approvals()
    {
        return $this->hasMany(PettyCashApproval::class);
    }
}
