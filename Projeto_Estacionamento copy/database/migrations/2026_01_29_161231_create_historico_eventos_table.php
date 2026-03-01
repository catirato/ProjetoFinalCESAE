<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilizador_id')->constrained('utilizador');
            $table->enum('tipo_evento', ['RESERVA', 'LISTA_ESPERA', 'REPORT', 'PONTOS']);
            $table->integer('entidade_id');
            $table->enum('acao', ['CRIADO', 'ATUALIZADO', 'REMOVIDO', 'VALIDADO', 'CANCELADO']);
            $table->string('descricao');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_eventos');
    }
};
