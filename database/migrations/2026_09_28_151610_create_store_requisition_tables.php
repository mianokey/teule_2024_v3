<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * ============================================================
         * STORE REQUISITIONS
         * ============================================================
         *
         * Base requisition tables.
         *
         * Routing fields:
         *   requisition_type
         *   source_store_id
         *   destination_store_id
         *
         * are added by the later migration:
         *
         * 2026_09_29_092233_add_type_and_stores_to_store_requisitions_table
         *
         * Fulfillment status is added by:
         *
         * 2026_09_29_093217_add_fulfillment_status_to_store_requisitions_table
         */

        Schema::create('store_requisitions', function (Blueprint $table) {
            $table->id();

            $table->string('requisition_number')->unique();

            $table->foreignId('requested_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('child_id')
                ->nullable()
                ->constrained('children')
                ->nullOnDelete();

            $table->string('department')->nullable();

            $table->text('purpose');

            $table->string('status', 30)
                ->default('DRAFT');

            $table->string('approval_stage', 30)
                ->nullable();

            $table->text('submission_notes')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(
                ['requested_by', 'status'],
                'store_requisitions_requester_status_idx'
            );

            $table->index(
                ['department', 'status'],
                'store_requisitions_department_status_idx'
            );

            $table->index(
                ['child_id', 'status'],
                'store_requisitions_child_status_idx'
            );
        });


        /*
         * ============================================================
         * REQUISITION ITEMS
         * ============================================================
         */


/*
 * ============================================================
 * REQUISITION ITEMS
 * ============================================================
 */

Schema::create('store_requisition_items', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('store_requisition_id');

    $table->unsignedBigInteger('store_item_id');

    $table->unsignedBigInteger('variant_id')
        ->nullable();

    $table->decimal('requested_quantity', 15, 3);

    $table->decimal('approved_quantity', 15, 3)
        ->default(0);

    $table->decimal('issued_quantity', 15, 3)
        ->default(0);

    $table->decimal('outstanding_quantity', 15, 3)
        ->default(0);

    $table->text('notes')->nullable();

    $table->timestamps();

    /*
     * Indexes
     */
    $table->index(
        'store_item_id',
        'store_requisition_items_store_item_id_foreign'
    );

    $table->index(
        'variant_id',
        'store_requisition_items_variant_id_foreign'
    );

    $table->index(
        ['store_requisition_id', 'store_item_id', 'variant_id'],
        'requisition_items_lookup_idx'
    );

    /*
     * Foreign keys
     */
    $table->foreign(
        'store_requisition_id',
        'store_requisition_items_store_requisition_id_foreign'
    )
        ->references('id')
        ->on('store_requisitions')
        ->cascadeOnDelete()
        ->restrictOnUpdate();

    $table->foreign(
        'store_item_id',
        'store_requisition_items_store_item_id_foreign'
    )
        ->references('id')
        ->on('store_items')
        ->restrictOnDelete()
        ->restrictOnUpdate();

    $table->foreign(
        'variant_id',
        'store_requisition_items_variant_id_foreign'
    )
        ->references('id')
        ->on('store_item_variants')
        ->restrictOnDelete()
        ->restrictOnUpdate();
});



        /*
         * ============================================================
         * REQUISITION APPROVALS
         * ============================================================
         */

        Schema::create('store_requisition_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_requisition_id')
                ->constrained('store_requisitions')
                ->cascadeOnDelete();

            $table->foreignId('approved_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('approval_level', 30);

            $table->string('decision', 30);

            $table->text('comments')->nullable();

            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            $table->index(
                ['store_requisition_id', 'approval_level'],
                'requisition_approvals_lookup_idx'
            );

            $table->index(
                ['approved_by', 'decision'],
                'requisition_approvals_approver_decision_idx'
            );
        });


        /*
         * ============================================================
         * REQUISITION COMMENTS / REVISION HISTORY
         * ============================================================
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
        /*
         * Drop child tables first because they depend on
         * store_requisitions.
         */
        Schema::dropIfExists('store_requisition_comments');
        Schema::dropIfExists('store_requisition_approvals');
        Schema::dropIfExists('store_requisition_items');
        Schema::dropIfExists('store_requisitions');
    }
};

