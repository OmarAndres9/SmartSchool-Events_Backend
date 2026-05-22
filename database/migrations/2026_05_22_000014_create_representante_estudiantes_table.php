<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representante_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representante_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('estudiante_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['representante_id', 'estudiante_id'], 'rep_est_unico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representante_estudiantes');
    }
};
