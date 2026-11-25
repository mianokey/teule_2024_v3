
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_collector_messages', function (Blueprint $table) {
            $table->id();

            // Device that collected and uploaded the SMS
            $table->foreignId('mpesa_collector_device_id')
                ->constrained('mpesa_collector_devices');

            // Original SMS information
            $table->string('sender', 100);
            $table->longText('raw_message');
            $table->timestamp('message_received_at')->nullable();

            // Used to prevent duplicate uploads from the same device
            $table->char('message_hash', 64);

            // Processing information
            $table->string('processing_status', 30)
                ->default('pending')
                ->index();

            $table->text('processing_error')->nullable();
            $table->timestamp('processed_at')->nullable();

            // Additional Android metadata, such as SIM slot
            $table->json('metadata')->nullable();

            $table->timestamp('uploaded_at')->useCurrent();

            $table->timestamps();

            // A device cannot upload the same message twice
            $table->unique(
                ['mpesa_collector_device_id', 'message_hash'],
                'mpesa_device_message_unique'
            );

            $table->index('message_received_at');
            $table->index(
    ['processing_status', 'message_received_at'],
    'mpesa_msg_status_received_idx'
);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_collector_messages');
    }
};
