<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('mei_id')->constrained('mei_profile')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onUpdate('restrict')->onDelete('set null');

            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->date('payment_date')->nullable();

            $table->tinyInteger('status')->default(1); // status padrão 1

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('charges');
    }
};
