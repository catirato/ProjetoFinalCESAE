<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimento_pontos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilizador_id')->constrained('utilizador');
            $table->foreignId('reserva_id')->nullable()->constrained('reserva');
            $table->enum('tipo', ['RESERVA', 'CANCELAMENTO', 'FALTA', 'RESET_MENSAL', 'AJUSTE']);
            $table->integer('pontos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimento_pontos');
    }
};
