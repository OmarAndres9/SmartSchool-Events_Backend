<?php

namespace App\Console\Commands;

use App\Models\Eventos;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerarEventosRecurrentes extends Command
{
    protected $signature = 'eventos:generar-recurrentes';
    protected $description = 'Genera instancias de eventos recurrentes hasta su fecha fin';

    public function handle(): int
    {
        $generados = 0;

        Eventos::where('es_recurrente', true)
            ->where(function ($q) {
                $q->whereNull('fecha_fin_recurrencia')
                  ->orWhere('fecha_fin_recurrencia', '>=', now());
            })
            ->chunk(50, function ($eventos) use (&$generados) {
                foreach ($eventos as $evento) {
                    $generados += $this->generarInstancias($evento);
                }
            });

        $this->info("Se generaron {$generados} instancias.");
        return Command::SUCCESS;
    }

    private function generarInstancias(Eventos $evento): int
    {
        $contador = 0;
        $fechas = $this->calcularFechas($evento);

        $existentes = Eventos::where('evento_origen_id', $evento->id)
            ->pluck('fecha_inicio')
            ->map(fn($d) => $d->toDateTimeString())
            ->toArray();

        foreach ($fechas as $fecha) {
            if (in_array($fecha->toDateTimeString(), $existentes)) {
                continue;
            }

            $duracion = $evento->fecha_fin
                ? $evento->fecha_inicio->diffInSeconds($evento->fecha_fin)
                : 3600;

            Eventos::create([
                'nombre'          => $evento->nombre,
                'descripcion'     => $evento->descripcion,
                'fecha_inicio'    => $fecha,
                'fecha_fin'       => $fecha->copy()->addSeconds($duracion),
                'lugar'           => $evento->lugar,
                'tipo_evento'     => $evento->tipo_evento,
                'modalidad'       => $evento->modalidad,
                'grupo_destinado' => $evento->grupo_destinado,
                'creado_por'      => $evento->creado_por,
                'evento_origen_id'=> $evento->id,
            ]);

            $contador++;
        }

        return $contador;
    }

    private function calcularFechas(Eventos $evento): array
    {
        $fechas = [];
        $inicio = $evento->fecha_inicio->copy();
        $finRec = $evento->fecha_fin_recurrencia;
        $intervalo = $evento->intervalo ?: 1;
        $limite = 100;
        $maxFechas = 365;

        match ($evento->tipo_recurrencia) {
            'diario' => $this->generarDiario($fechas, $inicio, $finRec, $intervalo, $limite, $maxFechas),
            'semanal' => $this->generarSemanal($fechas, $evento, $inicio, $finRec, $intervalo, $limite, $maxFechas),
            'quincenal' => $this->generarDiario($fechas, $inicio, $finRec, 14, $limite, $maxFechas),
            'mensual' => $this->generarMensual($fechas, $inicio, $finRec, $intervalo, $limite, $maxFechas),
            'anualmente' => $this->generarAnual($fechas, $inicio, $finRec, $intervalo, $limite, $maxFechas),
            default => [],
        };

        return $fechas;
    }

    private function generarDiario(array &$fechas, Carbon $inicio, ?Carbon $finRec, int $intervalo, int &$limite, int $max): void
    {
        $fecha = $inicio->copy();
        while ($limite > 0 && count($fechas) < $max) {
            $fecha->addDays($intervalo);
            if ($finRec && $fecha->gt($finRec)) break;
            $fechas[] = $fecha->copy();
            $limite--;
        }
    }

    private function generarSemanal(array &$fechas, Eventos $evento, Carbon $inicio, ?Carbon $finRec, int $intervalo, int &$limite, int $max): void
    {
        $diasSemana = $evento->dias_semana ?? [];
        if (empty($diasSemana)) {
            $diasSemana = ['L'];
        }

        $mapa = ['D' => 0, 'L' => 1, 'M' => 2, 'MX' => 3, 'J' => 4, 'V' => 5, 'S' => 6];
        $diasNum = array_map(fn($d) => $mapa[$d] ?? null, $diasSemana);
        $diasNum = array_filter($diasNum);

        $semanaBase = $inicio->copy()->startOfWeek();
        $semana = $semanaBase->copy()->addWeeks($intervalo);

        while ($limite > 0 && count($fechas) < $max) {
            foreach ($diasNum as $dia) {
                $fecha = $semana->copy()->addDays($dia)->setTime(
                    $inicio->hour, $inicio->minute, $inicio->second
                );

                if ($fecha->lte($inicio)) continue;
                if ($finRec && $fecha->gt($finRec)) break 2;

                $fechas[] = $fecha->copy();
                $limite--;
            }
            $semana->addWeeks($intervalo);
        }
    }

    private function generarMensual(array &$fechas, Carbon $inicio, ?Carbon $finRec, int $intervalo, int &$limite, int $max): void
    {
        $fecha = $inicio->copy();
        while ($limite > 0 && count($fechas) < $max) {
            $fecha->addMonths($intervalo);
            if ($finRec && $fecha->gt($finRec)) break;
            $fechas[] = $fecha->copy();
            $limite--;
        }
    }

    private function generarAnual(array &$fechas, Carbon $inicio, ?Carbon $finRec, int $intervalo, int &$limite, int $max): void
    {
        $fecha = $inicio->copy();
        while ($limite > 0 && count($fechas) < $max) {
            $fecha->addYears($intervalo);
            if ($finRec && $fecha->gt($finRec)) break;
            $fechas[] = $fecha->copy();
            $limite--;
        }
    }
}
