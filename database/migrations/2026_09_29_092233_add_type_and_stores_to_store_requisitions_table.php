<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_requisitions', function (Blueprint $table) {
            $table->string('requisition_type', 20)
                ->default('ITEM')
                ->after('requisition_number');

            $table->foreignId('source_store_id')
                ->nullable()
                ->after('requisition_type')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->foreignId('destination_store_id')
                ->nullable()
                ->after('source_store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->index('requisition_type');
        });
    }

    public function down(): void
    {
        Schema::table('store_requisitions', function (Blueprint $table) {
            $table->dropForeign(['source_store_id']);
            $table->dropForeign(['destination_store_id']);

            $table->dropIndex([
                'requisition_type',
            ]);

            $table->dropColumn([
                'requisition_type',
                'source_store_id',
                'destination_store_id',
            ]);
        });
    }
};