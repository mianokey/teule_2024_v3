<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_stocks', function (Blueprint $table) {
            $table->dropUnique('store_stocks_store_id_store_item_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('store_stocks', function (Blueprint $table) {
            $table->unique(
                ['store_id', 'store_item_id'],
                'store_stocks_store_id_store_item_id_unique'
            );
        });
    }
};