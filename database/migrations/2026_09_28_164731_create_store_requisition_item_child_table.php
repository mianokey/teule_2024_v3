<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_requisition_item_child', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_requisition_item_id')
                ->constrained('store_requisition_items')
                ->cascadeOnDelete();

            $table->foreignId('child_id')
                ->constrained('children')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
    ['store_requisition_item_id', 'child_id'],
    'req_item_child_unique'
);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_requisition_item_child');
    }
};

