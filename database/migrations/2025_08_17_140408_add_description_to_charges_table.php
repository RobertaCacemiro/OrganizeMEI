<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->text('description')->nullable()->after('amount');
            // "after('amount')" coloca a coluna depois do valor, mas pode mudar se quiser
        });
    }

    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
