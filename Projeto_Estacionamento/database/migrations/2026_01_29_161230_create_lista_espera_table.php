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
        Schema::create('lista_espera', function (Blueprint $table) {
            $table->id();

            $table->foreignId('utilizador_id')
                ->constrained('utilizador')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('data');
            $table->enum('estado', ['ATIVO', 'NOTIFICADO', 'ACEITE', 'EXPIRADO']);
            $table->integer('prioridade');

            $table->unique(['utilizador_id', 'data']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_espera');
    }
};
