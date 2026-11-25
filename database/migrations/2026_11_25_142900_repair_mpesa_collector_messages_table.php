<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpesa_collector_messages', function (Blueprint $table) {
            $table->foreignId('mpesa_collector_device_id')
                ->after('id')
                ->constrained('mpesa_collector_devices');

            $table->string('sender', 100)->after('mpesa_collector_device_id');
            $table->longText('raw_message')->after('sender');
            $table->timestamp('message_received_at')->nullable()->after('raw_message');

            $table->char('message_hash', 64)->after('message_received_at');

            $table->string('processing_status', 30)
                ->default('pending')
                ->index()
                ->after('message_hash');

            $table->text('processing_error')->nullable()->after('processing_status');
            $table->timestamp('processed_at')->nullable()->after('processing_error');
            $table->json('metadata')->nullable()->after('processed_at');
            $table->timestamp('uploaded_at')->useCurrent()->after('metadata');

            $table->unique(
                ['mpesa_collector_device_id', 'message_hash'],
                'mpesa_device_message_unique'
            );

            $table->index('message_received_at');
            $table->index(
                ['processing_status', 'message_received_at'],
                'mpesa_message_processing_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_collector_messages', function (Blueprint $table) {
            $table->dropForeign(['mpesa_collector_device_id']);
            $table->dropUnique('mpesa_device_message_unique');
            $table->dropIndex(['processing_status', 'message_received_at']);
            $table->dropIndex(['message_received_at']);
            
            $table->dropColumn([
                'mpesa_collector_device_id',
                'sender',
                'raw_message',
                'message_received_at',
                'message_hash',
                'processing_status',
                'processing_error',
                'processed_at',
                'metadata',
                'uploaded_at',
            ]);
        });
    }
};
