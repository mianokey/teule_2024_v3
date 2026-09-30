<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_fulfillments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_requisition_id')
                ->constrained('store_requisitions')
                ->restrictOnDelete();

            $table->string('transaction_number', 40)
                ->unique();

            $table->string('transaction_type', 20);

            $table->foreignId('source_store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->foreignId('destination_store_id')
                ->nullable()
                ->constrained('stores')
                ->restrictOnDelete();

            $table->foreignId('processed_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'store_requisition_id',
                'transaction_type',
            ]);
        });

        Schema::create('store_fulfillment_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_fulfillment_id')
                ->constrained('store_fulfillments')
                ->cascadeOnDelete();

            $table->foreignId('store_requisition_item_id')
                ->constrained('store_requisition_items')
                ->restrictOnDelete();

            $table->foreignId('store_item_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('store_item_variants')
                ->restrictOnDelete();

            $table->decimal('quantity', 15, 3);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_fulfillment_items');
        Schema::dropIfExists('store_fulfillments');
    }
};