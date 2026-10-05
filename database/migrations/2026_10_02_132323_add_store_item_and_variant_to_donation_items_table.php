<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_items', function (Blueprint $table) {

            $table->foreignId('store_item_id')
                ->nullable()
                ->after('donation_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->after('store_item_id')
                ->constrained('store_item_variants')
                ->restrictOnDelete();

            $table->index(
                ['store_item_id', 'variant_id'],
                'donation_items_store_item_variant_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('donation_items', function (Blueprint $table) {

            $table->dropForeign([
                'store_item_id',
            ]);

            $table->dropForeign([
                'variant_id',
            ]);

            $table->dropIndex(
                'donation_items_store_item_variant_idx'
            );

            $table->dropColumn([
                'store_item_id',
                'variant_id',
            ]);
        });
    }
};