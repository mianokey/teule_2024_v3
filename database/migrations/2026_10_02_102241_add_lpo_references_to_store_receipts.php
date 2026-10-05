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
        | Store Receipts
        |--------------------------------------------------------------------------
        */

        Schema::table('store_receipts', function (Blueprint $table) {
            $table->foreignId('supplier_id')
                ->nullable()
                ->after('store_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            $table->foreignId('store_lpo_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('store_lpos')
                ->restrictOnDelete();

            $table->index(
                ['supplier_id', 'status'],
                'store_receipts_supplier_status_idx'
            );

            $table->index(
                ['store_lpo_id', 'status'],
                'store_receipts_lpo_status_idx'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Store Receipt Items
        |--------------------------------------------------------------------------
        */

        Schema::table('store_receipt_items', function (Blueprint $table) {
            $table->foreignId('store_lpo_item_id')
                ->nullable()
                ->after('store_receipt_id')
                ->constrained('store_lpo_items')
                ->restrictOnDelete();

            $table->index(
                ['store_lpo_item_id'],
                'store_receipt_items_lpo_item_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('store_receipt_items', function (Blueprint $table) {
            $table->dropIndex('store_receipt_items_lpo_item_idx');
            $table->dropForeign(['store_lpo_item_id']);
            $table->dropColumn('store_lpo_item_id');
        });

        Schema::table('store_receipts', function (Blueprint $table) {
            $table->dropIndex('store_receipts_lpo_status_idx');
            $table->dropIndex('store_receipts_supplier_status_idx');

            $table->dropForeign(['store_lpo_id']);
            $table->dropForeign(['supplier_id']);

            $table->dropColumn([
                'store_lpo_id',
                'supplier_id',
            ]);
        });
    }
};

