<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_communications', function (Blueprint $table) {
            $table->timestamp('scheduled_at')
                ->nullable()
                ->after('status');

            $table->timestamp('cancelled_at')
                ->nullable()
                ->after('sent_at');

            $table->timestamp('delivered_at')
                ->nullable()
                ->after('cancelled_at');

            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('donation_communications', function (Blueprint $table) {
            $table->dropIndex([
                'scheduled_at',
            ]);

            $table->dropColumn([
                'scheduled_at',
                'cancelled_at',
                'delivered_at',
            ]);
        });
    }
};