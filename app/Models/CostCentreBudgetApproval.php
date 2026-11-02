<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostCentreBudgetApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'cost_centre_budget_id',
        'approval_stage',
        'decision',
        'approved_by',
        'return_reason',
        'comments',
        'rejection_reason',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(
            CostCentreBudget::class,
            'cost_centre_budget_id'
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}