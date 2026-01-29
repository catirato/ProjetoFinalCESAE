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
        Schema::create('utilizador', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email', 150)->unique();
            $table->string('password_hash');
            $table->boolean('obrigar_mudar_password')->default(true);
            $table->enum('role', ['ADMIN', 'SEGURANCA', 'COLAB']);
            $table->integer('pontos')->default(30);
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilizador');
    }
};
