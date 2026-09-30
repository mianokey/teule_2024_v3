<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_requisitions', function (Blueprint $table) {
            $table->string('fulfillment_status', 20)
                ->default('not_issued')
                ->after('approval_stage');

            $table->index('fulfillment_status');
        });
    }

    public function down(): void
    {
        Schema::table('store_requisitions', function (Blueprint $table) {
            $table->dropIndex([
                'fulfillment_status',
            ]);

            $table->dropColumn('fulfillment_status');
        });
    }
};