<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exclusão lógica: dados de saúde ocupacional não podem ser
     * destruídos fisicamente por engano.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('health_records', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('health_records', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
