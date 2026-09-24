<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('donation_id')
                ->constrained('donations')
                ->cascadeOnDelete();

            $table->string('item');

            $table->decimal('quantity', 12, 2)
                ->default(1);

            $table->string('unit')->nullable();

            /*
             * Optional estimated monetary value.
             * We don't require this for in-kind donations.
             */
            $table->decimal('estimated_value', 15, 2)
                ->nullable();

            $table->string('condition')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_items');
    }
};