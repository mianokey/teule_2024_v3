<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_centres', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('code', 50);
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['department_id', 'name'],
                'cost_centres_department_name_unique'
            );

            $table->unique(
                ['department_id', 'code'],
                'cost_centres_department_code_unique'
            );

            $table->index(['department_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centres');
    }
};
