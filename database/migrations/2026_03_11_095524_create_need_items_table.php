<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('need_items', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto-increment
            $table->string('icon')->default('flaticon-charity');
            $table->text('title');
            $table->text('description');
            $table->timestamps(); // creates created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('need_items');
    }
};
