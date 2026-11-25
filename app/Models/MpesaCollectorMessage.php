<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaCollectorMessage extends Model
{
    use HasFactory;

    protected $table = 'mpesa_collector_messages';

    protected $fillable = [
        'mpesa_collector_device_id',
        'sender',
        'raw_message',
        'message_received_at',
        'message_hash',
        'processing_status',
        'processing_error',
        'processed_at',
        'metadata',
        'uploaded_at',
    ];

    protected $casts = [
        'message_received_at' => 'datetime',
        'processed_at' => 'datetime',
        'uploaded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(
            MpesaCollectorDevice::class,
            'mpesa_collector_device_id'
        );
    }
    public function transaction()
{
    return $this->hasOne(
        MpesaCollectorTransaction::class,
        'mpesa_collector_message_id'
    );
}

}
