<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Índices de rendimiento — SmartSchool Events
 *
 * Problema: las consultas sobre eventos, recursos y notificaciones
 * hacen full-table-scan porque ninguna columna de búsqueda/filtro
 * tiene índice. Esta migración los agrega sin alterar datos.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Tabla eventos ─────────────────────────────────────────
        Schema::table('eventos', function (Blueprint $table) {
            // Consultas más frecuentes: getMisEventos filtra por creado_por
            // y ordena por fecha_inicio
            $table->index('creado_por',   'idx_eventos_creado_por');
            $table->index('fecha_inicio', 'idx_eventos_fecha_inicio');
            $table->index('tipo_evento',  'idx_eventos_tipo_evento');
            // Índice compuesto para la query principal de mis-eventos
            $table->index(['creado_por', 'fecha_inicio'], 'idx_eventos_user_fecha');
        });

        // ── Tabla pivot _evento_recurso_ ──────────────────────────
        Schema::table('_evento_recurso_', function (Blueprint $table) {
            // Los joins en belongsToMany usan evento_id y recurso_id
            // La FK de foreignId ya crea un índice en algunos motores,
            // pero lo declaramos explícitamente para MySQL/MariaDB
            $table->index('evento_id',  'idx_er_evento_id');
            $table->index('recurso_id', 'idx_er_recurso_id');
        });

        // ── Tabla _recursos__table ────────────────────────────────
        Schema::table('_recursos__table', function (Blueprint $table) {
            $table->index('estado', 'idx_recursos_estado');
            $table->index('tipo',   'idx_recursos_tipo');
        });

        // ── Tabla notificaciones ──────────────────────────────────
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->index('id_usuario', 'idx_notif_usuario');
            $table->index('id_evento',  'idx_notif_evento');
            $table->index('tipo',       'idx_notif_tipo');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropIndex('idx_eventos_creado_por');
            $table->dropIndex('idx_eventos_fecha_inicio');
            $table->dropIndex('idx_eventos_tipo_evento');
            $table->dropIndex('idx_eventos_user_fecha');
        });

        Schema::table('_evento_recurso_', function (Blueprint $table) {
            $table->dropIndex('idx_er_evento_id');
            $table->dropIndex('idx_er_recurso_id');
        });

        Schema::table('_recursos__table', function (Blueprint $table) {
            $table->dropIndex('idx_recursos_estado');
            $table->dropIndex('idx_recursos_tipo');
        });

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropIndex('idx_notif_usuario');
            $table->dropIndex('idx_notif_evento');
            $table->dropIndex('idx_notif_tipo');
        });
    }
};
