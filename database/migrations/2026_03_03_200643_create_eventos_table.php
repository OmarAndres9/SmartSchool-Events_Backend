<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->text('descripcion')->nullable();

            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');

            $table->string('lugar')->nullable();
            $table->string('tipo_evento');
            $table->string('modalidad');
            $table->string('grupo_destinado');

            $table->foreignId('creado_por')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};