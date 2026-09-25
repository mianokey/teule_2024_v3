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
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();

            /*
             * M-Pesa transaction details
             */
            $table->string('transaction_id')->unique();
            $table->string('transaction_type')->nullable();
            $table->string('trans_time')->nullable();

            $table->decimal('amount', 15, 2)->nullable();

            /*
             * Paybill / account information
             */
            $table->string('business_short_code')->nullable();
            $table->string('bill_ref_number')->nullable();
            $table->string('invoice_number')->nullable();

            /*
             * M-Pesa customer information
             */
            $table->string('phone_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            /*
             * Other M-Pesa response information
             */
            $table->string('org_account_balance')->nullable();
            $table->string('third_party_trans_id')->nullable();

            /*
             * Our internal review information
             */
            $table->foreignId('donor_id')
                ->nullable()
                ->constrained('donors')
                ->nullOnDelete();

            $table->foreignId('donation_id')
                ->nullable()
                ->constrained('donations')
                ->nullOnDelete();

            $table->enum('classification', [
                'donation',
                'payment',
                'refund',
                'other',
                'unclassified',
            ])->default('unclassified');

            $table->enum('status', [
                'pending_review',
                'confirmed',
                'rejected',
            ])->default('pending_review');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->text('notes')->nullable();

            /*
             * Preserve the complete Safaricom callback.
             * This is useful for auditing and troubleshooting.
             */
            $table->json('raw_payload')->nullable();

            $table->timestamp('received_at')->nullable();

            $table->timestamps();

            /*
             * Useful indexes for the review screen.
             */
            $table->index('status');
            $table->index('classification');
            $table->index('phone_number');
            $table->index('bill_ref_number');
            $table->index('trans_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};