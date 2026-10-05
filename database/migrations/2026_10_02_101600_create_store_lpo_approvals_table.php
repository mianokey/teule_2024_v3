<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_lpo_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_lpo_id')
                ->constrained('store_lpos')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * HOD
             * MANAGEMENT
             */
            $table->string('approval_stage', 30);

            /*
             * APPROVED
             * REJECTED
             * RETURNED
             */
            $table->string('action', 30);

            $table->text('comments')->nullable();

            $table->timestamp('acted_at')->nullable();

            $table->timestamps();

            $table->index(
                ['store_lpo_id', 'approval_stage'],
                'lpo_approvals_stage_idx'
            );

            $table->index(
                ['store_lpo_id', 'action'],
                'lpo_approvals_action_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_lpo_approvals');
    }
};

