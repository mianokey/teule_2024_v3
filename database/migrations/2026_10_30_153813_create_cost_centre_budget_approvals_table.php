<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_centre_budget_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cost_centre_budget_id')
                ->constrained('cost_centre_budgets')
                ->restrictOnDelete();

            // finance, dcm or cm
            $table->string('approval_stage', 30);

            // approved or rejected
            $table->enum('decision', ['approved', 'rejected']);

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('comments')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->timestamp('acted_at')->nullable();

            $table->timestamps();

            $table->index(
                ['cost_centre_budget_id', 'approval_stage'],
                'budget_approval_stage_index'
            );

            $table->index(
                ['approved_by', 'acted_at'],
                'budget_approval_user_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centre_budget_approvals');
    }
};