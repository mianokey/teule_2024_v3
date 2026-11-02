<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_centre_budgets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('finance_period_id')
                ->constrained('finance_periods')
                ->restrictOnDelete();

            $table->foreignId('cost_centre_id')
                ->constrained('cost_centres')
                ->restrictOnDelete();

            // Amount requested by Finance for this cost centre.
            $table->decimal('requested_amount', 15, 2)->default(0);

            // Budget authority approved for this cost centre.
            $table->decimal('approved_amount', 15, 2)->nullable();

            // Budget authority carried forward from a previous period.
            // This is not the same as actual cash carried forward.
            $table->decimal('carried_forward_amount', 15, 2)->default(0);

            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
            ])->default('draft');

            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['finance_period_id', 'cost_centre_id'],
                'finance_period_cost_centre_unique'
            );

            $table->index(['status', 'finance_period_id']);
            $table->index('cost_centre_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centre_budgets');
    }
};