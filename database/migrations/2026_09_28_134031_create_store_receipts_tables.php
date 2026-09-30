<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_receipts', function (Blueprint $table) {
            $table->id();

            $table->string('receipt_number', 50)->unique();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->string('source_type', 30);

            $table->string('supplier_name')->nullable();
            $table->string('supplier_reference')->nullable();

            $table->foreignId('donation_id')
                ->nullable()
                ->constrained('donations')
                ->nullOnDelete();

            $table->date('received_date');

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status', 30)->default('DRAFT');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['store_id', 'received_date']);
            $table->index(['source_type', 'status']);
            $table->index('donation_id');
        });

        Schema::create('store_receipt_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_receipt_id')
                ->constrained('store_receipts')
                ->cascadeOnDelete();

            $table->foreignId('store_item_id')
                ->constrained('store_items')
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('store_item_variants')
                ->restrictOnDelete();

            $table->decimal('quantity', 15, 3);

            $table->text('notes')->nullable();

            $table->timestamps();

$table->index(
    ['store_receipt_id', 'store_item_id', 'variant_id'],
    'receipt_items_lookup_idx'
);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_receipt_items');
        Schema::dropIfExists('store_receipts');
    }
};