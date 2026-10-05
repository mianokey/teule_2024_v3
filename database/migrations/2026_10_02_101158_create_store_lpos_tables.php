php
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
        | Store LPOs
        |--------------------------------------------------------------------------
        */

        Schema::create('store_lpos', function (Blueprint $table) {
            $table->id();

            $table->string('lpo_number', 50)->unique();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->date('lpo_date');
            $table->date('expected_delivery_date')->nullable();

            /*
             * DRAFT
             * SUBMITTED
             * APPROVED
             * PARTIALLY_RECEIVED
             * FULLY_RECEIVED
             * REJECTED
             * CANCELLED
             * CLOSED
             */
            $table->string('status', 30)->default('DRAFT');

            /*
             * Approval stage:
             *
             * NONE
             * HOD
             * MANAGEMENT
             * COMPLETED
             */
            $table->string('approval_stage', 30)->default('NONE');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(['supplier_id', 'status']);
            $table->index(['store_id', 'status']);
            $table->index(['status', 'approval_stage']);
            $table->index('lpo_date');
        });

        /*
        |--------------------------------------------------------------------------
        | Store LPO Items
        |--------------------------------------------------------------------------
        */

        Schema::create('store_lpo_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_lpo_id')
                ->constrained('store_lpos')
                ->cascadeOnDelete();

            $table->foreignId('store_item_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('store_item_variants')
                ->restrictOnDelete();

            /*
             * Description allows the LPO to retain the exact
             * description quoted/ordered from the supplier.
             */
            $table->text('description')->nullable();

            $table->decimal('ordered_quantity', 15, 3);

            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(
                ['store_lpo_id', 'store_item_id'],
                'lpo_items_lookup_idx'
            );

            $table->index(
                ['store_item_id', 'variant_id'],
                'lpo_items_item_variant_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_lpo_items');
        Schema::dropIfExists('store_lpos');
    }
};

