<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ref_one')->nullable();
            $table->string('ref_two')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payees');
    }
};
