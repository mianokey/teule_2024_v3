<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierAccountTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'transaction_date',
        'transaction_type',
        'reference_type',
        'reference_id',
        'reference_number',
        'description',
        'debit',
        'credit',
        'balance',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Supplier
    |--------------------------------------------------------------------------
    */

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | User Who Created The Transaction
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Referenced Document
    |--------------------------------------------------------------------------
    |
    | This is intentionally not a Laravel morphTo relationship because
    | reference_type is stored as a table/document name and we want
    | flexibility for supplier payments, LPOs, credit notes, etc.
    |
    */

    public function getReferenceModelAttribute()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        return null;
    }
}

