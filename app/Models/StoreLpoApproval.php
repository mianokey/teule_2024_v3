<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreLpoApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_lpo_id',
        'user_id',
        'approval_stage',
        'action',
        'comments',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function lpo(): BelongsTo
    {
        return $this->belongsTo(
            StoreLpo::class,
            'store_lpo_id'
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

