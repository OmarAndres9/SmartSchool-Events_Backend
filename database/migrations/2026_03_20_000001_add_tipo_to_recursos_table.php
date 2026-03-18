<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('_recursos__table', function (Blueprint $table) {
            $table->string('tipo')->nullable()->after('nombre');
            $table->string('capacidad')->nullable()->after('ubicacion');
            $table->text('descripcion')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('_recursos__table', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'capacidad', 'descripcion']);
        });
    }
};
