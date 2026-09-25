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
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            $table->timestamp('review_reminder_1_sent_at')
                ->nullable()
                ->after('received_at');

            $table->timestamp('review_reminder_2_sent_at')
                ->nullable()
                ->after('review_reminder_1_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'review_reminder_1_sent_at',
                'review_reminder_2_sent_at',
            ]);
        });
    }
};