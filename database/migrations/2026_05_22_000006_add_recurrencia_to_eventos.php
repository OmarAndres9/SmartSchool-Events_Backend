<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->boolean('es_recurrente')->default(false);
            $table->string('tipo_recurrencia', 20)->nullable();
            $table->unsignedTinyInteger('intervalo')->default(1);
            $table->json('dias_semana')->nullable();
            $table->date('fecha_fin_recurrencia')->nullable();
            $table->foreignId('evento_origen_id')->nullable()->constrained('eventos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('evento_origen_id');
            $table->dropColumn(['es_recurrente', 'tipo_recurrencia', 'intervalo', 'dias_semana', 'fecha_fin_recurrencia']);
        });
    }
};
