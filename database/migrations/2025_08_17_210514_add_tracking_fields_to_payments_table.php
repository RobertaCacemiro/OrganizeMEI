<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('user_id_sent');
            $table->timestamp('resent_at')->nullable()->after('resent_by');
            $table->timestamp('processing_at')->nullable()->after('resent_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['sent_at', 'resent_at', 'processing_at']);
        });
    }
};
