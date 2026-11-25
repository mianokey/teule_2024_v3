<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaCollectorTransaction extends Model
{
    use HasFactory;

    protected $table = 'mpesa_collector_transactions';

    protected $fillable = [
        'mpesa_collector_message_id',
        'receipt_number',
        'transaction_type',
        'direction',
        'payment_channel',
        'currency',
        'amount',
        'transaction_cost',
        'balance',
        'daily_limit_remaining',
        'counterparty_name',
        'counterparty_phone',
        'shortcode',
        'account_reference',
        'agent_number',
        'agent_name',
        'agent_outlet',
        'transaction_at',
        'parsed_data',
        'parser_version',
        'processing_status',
        'processing_error',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_cost' => 'decimal:2',
        'balance' => 'decimal:2',
        'daily_limit_remaining' => 'decimal:2',
        'transaction_at' => 'datetime',
        'parsed_data' => 'array',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(
            MpesaCollectorMessage::class,
            'mpesa_collector_message_id'
        );
    }
}
