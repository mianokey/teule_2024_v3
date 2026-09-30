<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreRequisitionApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_requisition_id',
        'approved_by',
        'approval_level',
        'decision',
        'comments',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(
            StoreRequisition::class,
            'store_requisition_id'
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }
}