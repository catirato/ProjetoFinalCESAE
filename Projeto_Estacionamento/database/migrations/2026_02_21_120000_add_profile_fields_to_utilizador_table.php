<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utilizador', function (Blueprint $table) {
            $table->string('telemovel', 20)->nullable()->after('email');
            $table->string('foto_perfil_path')->nullable()->after('telemovel');
        });
    }

    public function down(): void
    {
        Schema::table('utilizador', function (Blueprint $table) {
            $table->dropColumn(['telemovel', 'foto_perfil_path']);
        });
    }
};

