<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'rh', 'medico', 'tecnico'])->default('rh')->after('email');
            $table->string('cpf')->nullable()->after('role');
            $table->string('cargo')->nullable()->after('cpf');
            $table->string('setor')->nullable()->after('cargo');
            $table->date('data_admissao')->nullable()->after('setor');
            $table->date('data_demissao')->nullable()->after('data_admissao');
            $table->text('observacoes')->nullable()->after('data_demissao');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'cpf', 'cargo', 'setor', 'data_admissao', 'data_demissao', 'observacoes']);
        });
    }
};
