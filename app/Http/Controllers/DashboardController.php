<?php

namespace App\Http\Controllers;

use App\Models\Eventos;
use App\Models\Recursos;
use App\Models\User;
use App\Models\Notificaciones;
use App\Models\Reportes;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function stats()
    {
        $data = Cache::remember('dashboard_stats', 60, function () {
            $now = now();

            return [
                'eventos' => [
                    'total'       => Eventos::count(),
                    'proximos'    => Eventos::where('fecha_inicio', '>', $now)->count(),
                    'en_curso'    => Eventos::where('fecha_inicio', '<=', $now)
                        ->where(function ($q) use ($now) {
                            $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $now);
                        })->count(),
                    'finalizados' => Eventos::where('fecha_fin', '<', $now)->count(),
                ],
                'recursos' => [
                    'total'       => Recursos::count(),
                    'disponibles' => Recursos::where('estado', 'disponible')->count(),
                    'ocupados'    => Recursos::where('estado', 'ocupado')->count(),
                ],
                'usuarios' => User::count(),
                'inscripciones' => \DB::table('evento_inscripciones')->count(),
                'notificaciones' => Notificaciones::count(),
                'reportes' => Reportes::count(),
            ];
        });

        return response()->json($data);
    }
}
