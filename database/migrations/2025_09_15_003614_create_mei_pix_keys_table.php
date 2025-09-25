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
        Schema::create('mei_pix_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mei_profile_id')
                ->constrained('mei_profile')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('type', ['email', 'cpf', 'cnpj', 'phone', 'random']);
            $table->string('key_value');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mei_pix_keys');
    }
};
