<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaCollectorDevice extends Model
{
    use HasFactory;

    protected $table = 'mpesa_collector_devices';

    protected $fillable = [
        'device_id',
        'device_name',
        'location',
        'department',
        'status',
        'approved_by',
        'approved_at',
        'rejected_at',
        'revoked_at',
        'api_token_hash',
        'last_seen_at',
        'last_sync_at',
        'platform',
        'app_version',
        'admin_notes',
    ];

    protected $hidden = [
        'api_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'revoked_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'last_sync_at' => 'datetime',
        ];
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isActive(): bool
    {
        return $this->status === 'approved'
            && !empty($this->api_token_hash);
    }
    public function messages()
{
    return $this->hasMany(
        MpesaCollectorMessage::class,
        'mpesa_collector_device_id'
    );
}
public function syncLogs()
{
    return $this->hasMany(
        MpesaCollectorSyncLog::class,
        'mpesa_collector_device_id'
    );
}


}