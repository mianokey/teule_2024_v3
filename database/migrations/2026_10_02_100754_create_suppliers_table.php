<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Supplier Identification
            |--------------------------------------------------------------------------
            */

            $table->string('supplier_code', 50)->unique();
            $table->string('name');

            /*
            |--------------------------------------------------------------------------
            | Contact Information
            |--------------------------------------------------------------------------
            */

            $table->string('contact_person')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tax / Business Information
            |--------------------------------------------------------------------------
            */

            $table->string('tax_pin', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Commercial Terms
            |--------------------------------------------------------------------------
            */

            $table->string('payment_terms', 100)->nullable();

            $table->decimal('credit_limit', 15, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Opening Balance
            |--------------------------------------------------------------------------
            |
            | This is the supplier balance brought forward when the supplier
            | is first created.
            |
            | The supplier ledger will become the source of truth for the
            | actual running balance.
            |
            */

            $table->decimal('opening_balance', 15, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('name');
            $table->index('is_active');
            $table->index('tax_pin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};

