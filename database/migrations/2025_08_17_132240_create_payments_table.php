<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Relacionamento com users
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Relacionamento com mei_profile
            $table->foreignId('mei_id')
                ->constrained('mei_profile')
                ->onDelete('cascade');

            // Relacionamento com clients
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('cascade');

            // Status padrão 1 (pendente)
            $table->tinyInteger('status')->default(1);

            // Datas e valor
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date')->nullable();

            // Se foi enviado e quem reenviou
            $table->boolean('sent')->default(false);

            // quem reenviou (referência para users)
            $table->foreignId('resent_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
