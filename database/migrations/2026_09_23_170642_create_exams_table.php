<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['admissional', 'periodico', 'demissional', 'retorno', 'mudanca']);
            $table->date('data_exame');
            $table->date('data_vencimento')->nullable();
            $table->enum('status', ['pendente', 'realizado', 'vencido', 'cancelado'])->default('pendente');
            $table->string('medico_responsavel')->nullable();
            $table->text('resultado')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
