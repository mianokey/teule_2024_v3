<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE cost_centre_budgets
            MODIFY status ENUM(
                'draft',
                'submitted',
                'approved',
                'rejected',
                'returned'
            ) NOT NULL DEFAULT 'draft'
        ");

        DB::statement("
            ALTER TABLE cost_centre_budget_approvals
            MODIFY decision ENUM(
                'approved',
                'rejected',
                'sent_back'
            ) NOT NULL
        ");

        Schema::table('cost_centre_budgets', function (Blueprint $table) {
            $table->text('return_reason')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('cost_centre_budgets', function (Blueprint $table) {
            $table->dropColumn('return_reason');
        });

        // Keep the expanded enum values during rollback to avoid
        // accidentally invalidating records already using them.
    }
};
