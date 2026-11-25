<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaCollectorSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'mpesa_collector_device_id',
        'sync_reference',
        'messages_received',
        'messages_saved',
        'duplicates',
        'messages_processed',
        'messages_unrecognized',
        'messages_failed',
        'sync_status',
        'error_message',
        'started_at',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'messages_received' => 'integer',
        'messages_saved' => 'integer',
        'duplicates' => 'integer',
        'messages_processed' => 'integer',
        'messages_unrecognized' => 'integer',
        'messages_failed' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(
            MpesaCollectorDevice::class,
            'mpesa_collector_device_id'
        );
    }
}
