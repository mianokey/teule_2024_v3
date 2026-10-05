<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_pin',
        'payment_terms',
        'credit_limit',
        'opening_balance',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Supplier Account Transactions
    |--------------------------------------------------------------------------
    */

    public function accountTransactions(): HasMany
    {
        return $this->hasMany(
            SupplierAccountTransaction::class,
            'supplier_id'
        );
    }


}

