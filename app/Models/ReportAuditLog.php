<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'report_key',
        'report_title',
        'format',
        'filters',
        'date_from',
        'date_to',
        'row_count',
    ];

    protected $casts = [
        'filters' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
