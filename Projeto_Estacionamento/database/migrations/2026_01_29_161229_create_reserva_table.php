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
        Schema::create('reserva', function (Blueprint $table) {
            $table->id();

            $table->foreignId('utilizador_id')
                ->constrained('utilizador')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('lugar_id')
                ->constrained('lugar')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('data');
            $table->enum('estado', ['ATIVA', 'PRESENTE', 'NAO_COMPARECEU', 'CANCELADA']);
            $table->enum('modo_reserva', ['COLAB', 'ADMIN'])->default('COLAB');
            $table->enum('justificacao_tipo', ['EVENTO', 'OBRAS', 'MOBILIDADE_REDUZIDA', 'OUTRO'])->nullable();
            $table->text('justificacao_detalhe')->nullable();

            $table->foreignId('validada_por')
                ->nullable()
                ->constrained('utilizador')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva');
    }
};
