<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reserva', function (Blueprint $table) {
            if (!Schema::hasColumn('reserva', 'modo_reserva')) {
                $table->enum('modo_reserva', ['COLAB', 'ADMIN'])->default('COLAB')->after('estado');
            }

            if (!Schema::hasColumn('reserva', 'justificacao_tipo')) {
                $table->enum('justificacao_tipo', ['EVENTO', 'OBRAS', 'MOBILIDADE_REDUZIDA', 'OUTRO'])
                    ->nullable()
                    ->after('modo_reserva');
            }

            if (!Schema::hasColumn('reserva', 'justificacao_detalhe')) {
                $table->text('justificacao_detalhe')->nullable()->after('justificacao_tipo');
            }
        });

        // Ensure FK columns keep an index before dropping old composite uniques.
        try {
            DB::statement('CREATE INDEX reserva_utilizador_id_index ON reserva (utilizador_id)');
        } catch (\Throwable $e) {
            // index may already exist
        }

        try {
            DB::statement('CREATE INDEX reserva_lugar_id_index ON reserva (lugar_id)');
        } catch (\Throwable $e) {
            // index may already exist
        }

        try {
            DB::statement('ALTER TABLE reserva DROP INDEX reserva_utilizador_id_data_unique');
        } catch (\Throwable $e) {
            // index may not exist in some environments
        }

        try {
            DB::statement('ALTER TABLE reserva DROP INDEX reserva_lugar_id_data_unique');
        } catch (\Throwable $e) {
            // index may not exist in some environments
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE reserva ADD UNIQUE reserva_utilizador_id_data_unique (utilizador_id, data)');
        } catch (\Throwable $e) {
            // may fail if duplicates were created while admin override was enabled
        }

        try {
            DB::statement('ALTER TABLE reserva ADD UNIQUE reserva_lugar_id_data_unique (lugar_id, data)');
        } catch (\Throwable $e) {
            // may fail if duplicates were created while admin override was enabled
        }

        Schema::table('reserva', function (Blueprint $table) {
            if (Schema::hasColumn('reserva', 'justificacao_detalhe')) {
                $table->dropColumn('justificacao_detalhe');
            }
            if (Schema::hasColumn('reserva', 'justificacao_tipo')) {
                $table->dropColumn('justificacao_tipo');
            }
            if (Schema::hasColumn('reserva', 'modo_reserva')) {
                $table->dropColumn('modo_reserva');
            }
        });
    }
};
