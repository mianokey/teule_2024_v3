<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'starts_on',
        'ends_on',
        'status',
        'notes',
        'created_by',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'closed_at' => 'datetime',
    ];

    public function budgets(): HasMany
    {
        return $this->hasMany(CostCentreBudget::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function getNameAttribute(): string
    {
        return $this->starts_on->format('F Y');
    }
}