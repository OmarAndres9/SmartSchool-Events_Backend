<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('periodo_id')->constrained('periodos')->onDelete('cascade');
            $table->decimal('calificacion', 5, 2);
            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamps();
            $table->unique(['estudiante_id', 'materia_id', 'periodo_id'], 'nota_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
