<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreRequisition extends Model
{
    use HasFactory;

protected $fillable = [
    'requisition_number',
    'requested_by',
    'child_id',
    'department',
    'purpose',
    'status',
    'approval_stage',
    'submission_notes',
    'submitted_at',
    'approved_at',
    'completed_at',

    // Requisition routing
    'requisition_type',
    'source_store_id',
    'destination_store_id',
    'fulfillment_status',
];


    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            StoreRequisitionItem::class
        );
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(
            StoreRequisitionApproval::class
        );
    }

    public function comments(): HasMany
    {
        return $this->hasMany(
            StoreRequisitionComment::class
        );
    }

    public function sourceStore(): BelongsTo
{
    return $this->belongsTo(Store::class, 'source_store_id');
}

public function destinationStore(): BelongsTo
{
    return $this->belongsTo(Store::class, 'destination_store_id');
}

public function fulfillments(): HasMany
{
    return $this->hasMany(
        StoreFulfillment::class,
        'store_requisition_id'
    );
}

}