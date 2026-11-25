<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_collector_sync_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mpesa_collector_device_id')
                ->constrained('mpesa_collector_devices')
                ->cascadeOnDelete();

            $table->string('sync_reference', 100)->nullable()->index();

            $table->unsignedInteger('messages_received')->default(0);
            $table->unsignedInteger('messages_saved')->default(0);
            $table->unsignedInteger('duplicates')->default(0);
            $table->unsignedInteger('messages_processed')->default(0);
            $table->unsignedInteger('messages_unrecognized')->default(0);
            $table->unsignedInteger('messages_failed')->default(0);

            $table->string('sync_status', 30)->default('completed')->index();

            $table->text('error_message')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index([
                'mpesa_collector_device_id',
                'created_at'
            ], 'mpesa_sync_device_date_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_collector_sync_logs');
    }
};
