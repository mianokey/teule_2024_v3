<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpesa_collector_transactions', function (Blueprint $table) {
            $table->foreignId('mpesa_collector_message_id')
                ->after('id')
                ->unique()
                ->constrained('mpesa_collector_messages')
                ->cascadeOnDelete();

            $table->string('receipt_number', 50)->nullable()->index();
            $table->string('transaction_type', 50)->index();
            $table->string('direction', 20)->nullable()->index();
            $table->string('payment_channel', 50)->nullable();
            $table->string('currency', 10)->default('KES');

            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('transaction_cost', 15, 2)->nullable();
            $table->decimal('balance', 15, 2)->nullable();
            $table->decimal('daily_limit_remaining', 15, 2)->nullable();

            $table->string('counterparty_name')->nullable();
            $table->string('counterparty_phone', 30)->nullable();

            $table->string('shortcode', 50)->nullable();
            $table->string('account_reference', 100)->nullable();

            $table->string('agent_number', 50)->nullable();
            $table->string('agent_name')->nullable();
            $table->string('agent_outlet')->nullable();

            $table->timestamp('transaction_at')->nullable()->index();

            $table->json('parsed_data')->nullable();
            $table->string('parser_version', 30)->nullable();

            $table->string('processing_status', 30)
                ->default('processed')
                ->index();

            $table->text('processing_error')->nullable();

            $table->index(
                ['transaction_type', 'direction', 'transaction_at'],
                'mpesa_transaction_reporting_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_collector_transactions', function (Blueprint $table) {
            $table->dropForeign(['mpesa_collector_message_id']);
            $table->dropUnique(['mpesa_collector_message_id']);
            $table->dropIndex('mpesa_transaction_reporting_index');

            $table->dropColumn([
                'mpesa_collector_message_id',
                'receipt_number',
                'transaction_type',
                'direction',
                'payment_channel',
                'currency',
                'amount',
                'transaction_cost',
                'balance',
                'daily_limit_remaining',
                'counterparty_name',
                'counterparty_phone',
                'shortcode',
                'account_reference',
                'agent_number',
                'agent_name',
                'agent_outlet',
                'transaction_at',
                'parsed_data',
                'parser_version',
                'processing_status',
                'processing_error',
            ]);
        });
    }
};