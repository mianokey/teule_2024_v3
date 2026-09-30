<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreRequisitionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_requisition_id',
        'user_id',
        'comment_type',
        'comment',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(
            StoreRequisition::class,
            'store_requisition_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}