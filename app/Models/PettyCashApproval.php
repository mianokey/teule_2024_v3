<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'petty_cash_request_id',
        'user_id',
        'approved',
    ];

    public function pettyCashRequest()
    {
        return $this->belongsTo(PettyCashRequest::class);
    }
}
