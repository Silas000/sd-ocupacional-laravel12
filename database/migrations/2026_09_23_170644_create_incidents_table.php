<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('data_ocorrencia');
            $table->string('local');
            $table->text('descricao');
            $table->enum('severidade', ['leve', 'moderado', 'grave'])->default('moderado');
            $table->enum('tipo', ['acidente', 'incidente', 'quase_acidente'])->default('incidente');
            $table->text('medidas_corretivas')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
