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
        Schema::create('petty_cash_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payee_id')->constrained()->onDelete('cascade');
            $table->string('payment_medium')->nullable();
            $table->string('store_account')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('purpose');
            $table->text('details')->nullable();
            $table->string('status')->default('Awaiting 2 Approvals');
            $table->string('receipt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_requests');
    }
};
