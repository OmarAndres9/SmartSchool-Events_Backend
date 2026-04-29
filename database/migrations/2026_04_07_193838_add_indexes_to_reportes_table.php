<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * OPTIMIZACIÓN: índices en tabla de reportes.
 * La migración original estaba vacía — las queries de filtrado
 * (fecha, tipo, estado) hacían full-table-scan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('_reportes_', function (Blueprint $table) {
            $table->index('fecha',      'idx_reportes_fecha');
            $table->index('tipo',       'idx_reportes_tipo');
            $table->index('estado',     'idx_reportes_estado');
            $table->index('id_usuario', 'idx_reportes_usuario');
            $table->index('id_evento',  'idx_reportes_evento');
        });
    }

    public function down(): void
    {
        Schema::table('_reportes_', function (Blueprint $table) {
            $table->dropIndex('idx_reportes_fecha');
            $table->dropIndex('idx_reportes_tipo');
            $table->dropIndex('idx_reportes_estado');
            $table->dropIndex('idx_reportes_usuario');
            $table->dropIndex('idx_reportes_evento');
        });
    }
};
