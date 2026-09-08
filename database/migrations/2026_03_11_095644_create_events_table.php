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
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto-increment
            $table->date('date_from');
            $table->date('date_to');
            $table->text('title');
            $table->time('time_from');
            $table->time('time_to');
            $table->text('location');
            $table->text('url'); // image or file URL
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
