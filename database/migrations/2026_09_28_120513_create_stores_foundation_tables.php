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
        | Store Item Categories
        |--------------------------------------------------------------------------
        */

        Schema::create('store_item_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Store Units
        |--------------------------------------------------------------------------
        */

        Schema::create('store_units', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Stores
        |--------------------------------------------------------------------------
        */

        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Store Items
        |--------------------------------------------------------------------------
        */

        Schema::create('store_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('store_item_categories')
                ->restrictOnDelete();

            $table->foreignId('unit_id')
                ->constrained('store_units')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('sku', 100)->nullable()->unique();

            /*
             * CONSUMABLE
             * RETURNABLE
             * ASSET
             */
            $table->string('item_type', 20)->default('CONSUMABLE');

            $table->text('description')->nullable();

            /*
             * When stock falls at or below this level,
             * the system can flag the item for replenishment.
             */
            $table->decimal('reorder_level', 15, 3)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['category_id', 'item_type']);
            $table->index('name');
        });

        /*
        |--------------------------------------------------------------------------
        | Current Store Stock
        |--------------------------------------------------------------------------
        |
        | This is the current balance/cache.
        | The complete history remains in store_stock_movements.
        |
        */

        Schema::create('store_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->foreignId('store_item_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            $table->decimal('quantity', 15, 3)->default(0);

            $table->timestamp('last_movement_at')->nullable();

            $table->timestamps();

            $table->unique(['store_id', 'store_item_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Stock Movement Ledger
        |--------------------------------------------------------------------------
        */

        Schema::create('store_stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->foreignId('store_item_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            /*
             * RECEIPT
             * ISSUE
             * RETURN_IN
             * RETURN_OUT
             * TRANSFER_IN
             * TRANSFER_OUT
             * ADJUSTMENT
             */
            $table->string('movement_type', 30);

            /*
             * Always store the movement quantity as a
             * positive number. movement_type determines
             * whether it adds to or subtracts from stock.
             */
            $table->decimal('quantity', 15, 3);

            /*
             * Balance immediately after this movement.
             * Useful for audit and reporting.
             */
            $table->decimal('balance_after', 15, 3);

            /*
             * Optional reference to the document that caused
             * the movement.
             *
             * Examples:
             * store_receipts
             * store_issues
             * purchase_orders
             * requisitions
             */
            $table->nullableMorphs('reference');

            /*
             * User who performed/recorded the movement.
             */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['store_id', 'store_item_id', 'movement_type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        /*
         * Drop in reverse dependency order.
         */

        Schema::dropIfExists('store_stock_movements');
        Schema::dropIfExists('store_stocks');
        Schema::dropIfExists('store_items');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('store_units');
        Schema::dropIfExists('store_item_categories');
    }
};