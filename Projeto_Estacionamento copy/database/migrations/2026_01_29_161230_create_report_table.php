<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilizador_id')->constrained('utilizador');
            $table->enum('tipo', ['LUGAR_OCUPADO', 'SEM_RESERVA', 'PROBLEMA']);
            $table->text('descricao');
            $table->enum('estado', ['PENDENTE', 'VALIDADO', 'REJEITADO']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report');
    }
};
