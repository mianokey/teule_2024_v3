php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_account_transactions', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Transaction Information
            |--------------------------------------------------------------------------
            |
            | Examples:
            |
            | OPENING_BALANCE
            | PURCHASE
            | PAYMENT
            | CREDIT_NOTE
            | DEBIT_NOTE
            | ADJUSTMENT
            |
            */

            $table->date('transaction_date');

            $table->string('transaction_type', 30);

            /*
            |--------------------------------------------------------------------------
            | Source Document
            |--------------------------------------------------------------------------
            |
            | Allows a transaction to point back to the document that caused it.
            |
            | Examples:
            |
            | store_lpos
            | supplier_payments
            | supplier_credit_notes
            |
            */

            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            $table->text('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Debit / Credit
            |--------------------------------------------------------------------------
            |
            | Supplier account perspective:
            |
            | DEBIT  = amount owed to supplier increases
            | CREDIT = amount owed to supplier decreases
            |
            | Example:
            |
            | LPO/Purchase     Debit 100,000
            | Payment          Credit  60,000
            | Balance owed             40,000
            |
            */

            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Running Balance
            |--------------------------------------------------------------------------
            |
            | Snapshot of the supplier balance immediately after this
            | transaction.
            |
            */

            $table->decimal('balance', 15, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Additional Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['supplier_id', 'transaction_date'],
                'supplier_account_supplier_date_index'
            );

            $table->index(
                ['supplier_id', 'transaction_type'],
                'supplier_account_supplier_type_index'
            );

            $table->index(
                ['reference_type', 'reference_id'],
                'supplier_account_reference_index'
            );

            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_account_transactions');
    }
};

