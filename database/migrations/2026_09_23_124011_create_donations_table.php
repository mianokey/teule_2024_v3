<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            $table->string('donation_number')->unique();

            $table->foreignId('donor_id')
                ->nullable()
                ->constrained('donors')
                ->nullOnDelete();

            /*
             * cash = monetary donation
             * in_kind = goods/items
             */
            $table->enum('type', [
                'cash',
                'in_kind',
            ]);

            /*
             * Where the donation came from.
             * M-Pesa will be connected later.
             */
            $table->enum('source', [
                'manual',
                'mpesa',
                'bank',
                'other',
            ])->default('manual');

            /*
             * Allows us to distinguish an actual donation
             * from an incoming transaction that may later
             * turn out to be a payment, refund, etc.
             */
            $table->enum('classification', [
                'donation',
                'payment',
                'refund',
                'other',
                'unclassified',
            ])->default('donation');

            $table->decimal('amount', 15, 2)
                ->nullable();

            $table->string('currency', 3)
                ->default('KES');

            $table->date('donation_date');

            $table->string('purpose')->nullable();

            $table->string('reference')->nullable();

            $table->string('payment_reference')->nullable();

            $table->text('description')->nullable();

            $table->text('notes')->nullable();

            /*
             * User who recorded the donation.
             */
            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('donation_date');
            $table->index('source');
            $table->index('classification');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};