<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('_recursos__table', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('_reportes__table', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('eventos', fn(Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('_recursos__table', fn(Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('notificaciones', fn(Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('_reportes__table', fn(Blueprint $t) => $t->dropSoftDeletes());
    }
};
