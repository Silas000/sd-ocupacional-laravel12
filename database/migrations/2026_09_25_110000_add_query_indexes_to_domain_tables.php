<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices nos campos usados por filtros, ordenações e métricas dos
     * dashboards. As chaves estrangeiras já têm índice próprio.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->index('status', 'exams_status_index');
            $table->index('data_vencimento', 'exams_data_vencimento_index');
            $table->index('data_exame', 'exams_data_exame_index');
            $table->index(['status', 'data_vencimento'], 'exams_status_vencimento_index');
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->index('setor', 'risks_setor_index');
            $table->index('severidade', 'risks_severidade_index');
            $table->index('categoria', 'risks_categoria_index');
            $table->index(['setor', 'severidade'], 'risks_setor_severidade_index');
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->index('data_ocorrencia', 'incidents_data_ocorrencia_index');
            $table->index('severidade', 'incidents_severidade_index');
            $table->index('tipo', 'incidents_tipo_index');
        });

        Schema::table('health_records', function (Blueprint $table) {
            $table->index('data_registro', 'health_records_data_registro_index');
            $table->index('tipo', 'health_records_tipo_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('setor', 'users_setor_index');
            $table->index('role', 'users_role_index');
            $table->index(['role', 'setor'], 'users_role_setor_index');
        });

        Schema::table('password_history', function (Blueprint $table) {
            $table->index(['user_id', 'password'], 'password_history_user_password_index');
        });

        Schema::table('audits', function (Blueprint $table) {
            $table->index('event', 'audits_event_index');
            $table->index('user_id', 'audits_user_id_index');
            $table->index('created_at', 'audits_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex('exams_status_index');
            $table->dropIndex('exams_data_vencimento_index');
            $table->dropIndex('exams_data_exame_index');
            $table->dropIndex('exams_status_vencimento_index');
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->dropIndex('risks_setor_index');
            $table->dropIndex('risks_severidade_index');
            $table->dropIndex('risks_categoria_index');
            $table->dropIndex('risks_setor_severidade_index');
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropIndex('incidents_data_ocorrencia_index');
            $table->dropIndex('incidents_severidade_index');
            $table->dropIndex('incidents_tipo_index');
        });

        Schema::table('health_records', function (Blueprint $table) {
            $table->dropIndex('health_records_data_registro_index');
            $table->dropIndex('health_records_tipo_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_setor_index');
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_role_setor_index');
        });

        Schema::table('password_history', function (Blueprint $table) {
            $table->dropIndex('password_history_user_password_index');
        });

        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex('audits_event_index');
            $table->dropIndex('audits_user_id_index');
            $table->dropIndex('audits_created_at_index');
        });
    }
};
