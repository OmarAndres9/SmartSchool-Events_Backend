<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('_reportes_', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->text('descripcion');
            $table->dateTime('fecha');
            $table->string('estado', 20);
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_evento')->nullable();
            $table->foreign('id_evento')->references('id')->on('eventos')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_reportes_');
    }
};
