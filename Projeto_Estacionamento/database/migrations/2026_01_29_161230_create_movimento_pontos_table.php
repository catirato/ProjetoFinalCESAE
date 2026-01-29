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
       Schema::create('movimento_pontos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('utilizador_id')
                ->constrained('utilizador')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('reserva_id')
                ->nullable()
                ->constrained('reserva')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->enum('tipo', [
                'RESERVA',
                'CANCELAMENTO',
                'FALTA',
                'RESET_MENSAL',
                'AJUSTE'
            ]);

            $table->integer('pontos');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimento_pontos');
    }
};
