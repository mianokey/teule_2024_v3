<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_communications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('donation_id')
                ->nullable()
                ->constrained('donations')
                ->nullOnDelete();

            $table->foreignId('donor_id')
                ->nullable()
                ->constrained('donors')
                ->nullOnDelete();

            $table->foreignId('sent_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('channel', [
                'sms',
                'email',
            ]);

            $table->enum('type', [
                'thank_you',
                'follow_up',
                'other',
            ])->default('thank_you');

            $table->string('recipient', 255);

            $table->string('subject')->nullable();

            $table->text('message');

            $table->enum('status', [
                'pending',
                'sent',
                'failed',
            ])->default('pending');

            $table->string('provider_reference')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index('channel');
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_communications');
    }
};