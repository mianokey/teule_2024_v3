<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cost_centre_budgets', function (Blueprint $table) {
            $table->string('approval_stage', 30)
                ->nullable()
                ->after('status');

            $table->index(
                ['approval_stage', 'status'],
                'cost_centre_budget_current_stage_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('cost_centre_budgets', function (Blueprint $table) {
            $table->dropIndex('cost_centre_budget_current_stage_index');
            $table->dropColumn('approval_stage');
        });
    }
};