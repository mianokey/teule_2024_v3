<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SELLABLE ITEMS
        |--------------------------------------------------------------------------
        | Existing store items remain the source of stock.
        | Selling price is shared across departments.
        */

        Schema::table('store_items', function (Blueprint $table) {
            $table->boolean('is_sellable')
                ->default(false)
                ->after('is_active');

            $table->decimal('selling_price', 15, 2)
                ->default(0)
                ->after('is_sellable');

            $table->index('is_sellable');
        });

        /*
        |--------------------------------------------------------------------------
        | INTERNAL STOCK TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        Schema::create('internal_stock_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_number', 50)->unique();

            $table->foreignId('source_store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->foreignId('destination_store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->string('status', 30)->default('DRAFT');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('issued_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->timestamps();

            $table->index(['source_store_id', 'status']);
            $table->index(['destination_store_id', 'status']);
        });

        Schema::create('internal_stock_transaction_items', function (Blueprint $table) {
            $table->id();

$table->unsignedBigInteger('internal_stock_transaction_id');

$table->foreign('internal_stock_transaction_id', 'ist_items_transaction_fk')
    ->references('id')
    ->on('internal_stock_transactions')
    ->cascadeOnDelete();
    

            $table->foreignId('store_item_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('store_item_variants')
                ->restrictOnDelete();

            $table->decimal('quantity', 15, 3);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['store_item_id', 'variant_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | EXTERNAL SALES
        |--------------------------------------------------------------------------
        */

        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('sale_number', 50)->unique();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->string('customer_email')->nullable();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->string('status', 30)->default('DRAFT');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['department_id', 'status']);
            $table->index(['store_id', 'status']);
            $table->index('created_at');
        });

        Schema::create('sales_transaction_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_transaction_id')
                ->constrained('sales_transactions')
                ->cascadeOnDelete();

            $table->foreignId('store_item_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('store_item_variants')
                ->restrictOnDelete();

            $table->string('item_description')->nullable();

            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2);

            $table->timestamps();

            $table->index(['store_item_id', 'variant_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | SALE PAYMENTS
        |--------------------------------------------------------------------------
        | Supports split payments and M-Pesa allocation.
        */

        Schema::create('sales_transaction_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_transaction_id')
                ->constrained('sales_transactions')
                ->cascadeOnDelete();

            $table->string('payment_method', 30);
            // CASH, MPESA_STK, MPESA_PAYBILL, BANK, OTHER

            $table->decimal('amount', 15, 2);

            $table->string('payment_reference', 100)->nullable();

            $table->foreignId('mpesa_transaction_id')
                ->nullable()
                ->unique()
                ->constrained('mpesa_transactions')
                ->restrictOnDelete();

            $table->string('status', 30)->default('PENDING');
            // PENDING, COMPLETED, FAILED, REVERSED

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['sales_transaction_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_transaction_payments');
        Schema::dropIfExists('sales_transaction_items');
        Schema::dropIfExists('sales_transactions');
        Schema::dropIfExists('internal_stock_transaction_items');
        Schema::dropIfExists('internal_stock_transactions');

        Schema::table('store_items', function (Blueprint $table) {
            $table->dropIndex(['is_sellable']);
            $table->dropColumn(['is_sellable', 'selling_price']);
        });
    }
};
