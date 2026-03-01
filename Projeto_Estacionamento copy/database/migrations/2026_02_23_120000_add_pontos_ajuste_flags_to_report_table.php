<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report', function (Blueprint $table) {
            $table->boolean('ajuste_pontos_necessario')->default(false)->after('estado');
            $table->boolean('ajuste_pontos_concluido')->default(false)->after('ajuste_pontos_necessario');
        });
    }

    public function down(): void
    {
        Schema::table('report', function (Blueprint $table) {
            $table->dropColumn(['ajuste_pontos_necessario', 'ajuste_pontos_concluido']);
        });
    }
};
