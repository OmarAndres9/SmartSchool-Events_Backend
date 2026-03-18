<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FIX: fecha_fin y grupo_destinado eran NOT NULL en la migración original
     * pero el backend los acepta como nullable. Esta migración los corrige
     * en bases de datos que ya tienen la tabla creada.
     */
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dateTime('fecha_fin')->nullable()->change();
            $table->string('grupo_destinado')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dateTime('fecha_fin')->nullable(false)->change();
            $table->string('grupo_destinado')->nullable(false)->change();
        });
    }
};
