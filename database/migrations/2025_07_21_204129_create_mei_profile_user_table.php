<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mei_profile_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mei_profile_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps(); // created_at e updated_at

            $table->unique(['mei_profile_id', 'user_id']);

            $table->foreign('mei_profile_id')
                ->references('id')->on('mei_profile')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mei_profile_user');
    }
};
