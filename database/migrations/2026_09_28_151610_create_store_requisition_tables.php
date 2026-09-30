<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * The requisitions, requisition items and approval
         * tables already exist in the database.
         *
         * This migration only adds the missing comments /
         * revision-history table.
         */
        Schema::create('store_requisition_comments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('store_requisition_id')
                ->constrained('store_requisitions')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * SUBMISSION
             * APPROVAL
             * RETURN
             * REVISION
             * GENERAL
             */
            $table->string('comment_type', 30)
                ->default('GENERAL');

            $table->text('comment');

            $table->timestamps();

            $table->index(
                ['store_requisition_id', 'comment_type'],
                'requisition_comments_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_requisition_comments');
    }
};