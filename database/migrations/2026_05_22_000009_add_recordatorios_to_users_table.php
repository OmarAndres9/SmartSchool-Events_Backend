<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('recordatorio_email')->default(true);
            $table->integer('recordatorio_anticipacion_minutos')->default(1440);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['recordatorio_email', 'recordatorio_anticipacion_minutos']);
        });
    }
};
