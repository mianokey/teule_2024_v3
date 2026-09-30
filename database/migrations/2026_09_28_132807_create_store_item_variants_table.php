<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_item_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_item_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('code', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['store_item_id', 'is_active']);
            $table->unique(['store_item_id', 'name']);
        });

        Schema::table('store_stocks', function (Blueprint $table) {
            $table->foreignId('variant_id')
                ->nullable()
                ->after('store_item_id')
                ->constrained('store_item_variants')
                ->restrictOnDelete();

            $table->unique(
                ['store_id', 'store_item_id', 'variant_id'],
                'store_stocks_store_item_variant_unique'
            );
        });

        Schema::table('store_stock_movements', function (Blueprint $table) {
            $table->foreignId('variant_id')
                ->nullable()
                ->after('store_item_id')
                ->constrained('store_item_variants')
                ->restrictOnDelete();

            $table->index(
                ['store_id', 'store_item_id', 'variant_id'],
                'store_stock_movements_item_variant_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('store_stock_movements', function (Blueprint $table) {
            $table->dropIndex('store_stock_movements_item_variant_index');
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
        });

        Schema::table('store_stocks', function (Blueprint $table) {
            $table->dropUnique('store_stocks_store_item_variant_unique');
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
        });

        Schema::dropIfExists('store_item_variants');
    }
};