<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lista_espera', function (Blueprint $table) {
            $table->string('notification_token', 80)->nullable()->after('prioridade');
            $table->dateTime('notificado_em')->nullable()->after('notification_token');
            $table->dateTime('expira_em')->nullable()->after('notificado_em');
        });
    }

    public function down(): void
    {
        Schema::table('lista_espera', function (Blueprint $table) {
            $table->dropColumn(['notification_token', 'notificado_em', 'expira_em']);
        });
    }
};
