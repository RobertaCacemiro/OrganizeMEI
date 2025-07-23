<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mei_id')->constrained('mei_profile')->onDelete('cascade');

            // Campos do cliente
            $table->string('cpf_cnpj', 20)->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
