<?php

namespace Database\Seeders;

use App\Models\Materia;
use App\Models\Nota;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $periodo = Periodo::firstOrCreate(
            ['nombre' => 'Semestre 2026-1'],
            [
                'fecha_inicio' => '2026-02-01',
                'fecha_fin'    => '2026-06-30',
                'activo'       => true,
            ]
        );

        $docente = User::where('email', 'docente@smartschool.com')->first();
        $estudiante = User::where('email', 'estudiante@smartschool.com')->first();
        $acudiente = User::where('email', 'acudiente@smartschool.com')->first();

        if (! $docente || ! $estudiante || ! $acudiente) {
            $this->command->warn('Ejecutá primero AdminUserSeeder para crear los usuarios.');
            return;
        }

        $materias = [
            ['nombre' => 'Matemáticas',      'descripcion' => 'Álgebra y geometría'],
            ['nombre' => 'Lenguaje',          'descripcion' => 'Lengua y literatura'],
            ['nombre' => 'Ciencias Naturales','descripcion' => 'Biología y química básica'],
            ['nombre' => 'Inglés',            'descripcion' => 'Inglés nivel A2'],
            ['nombre' => 'Historia',          'descripcion' => 'Historia universal'],
        ];

        foreach ($materias as $data) {
            $materia = Materia::firstOrCreate(
                ['nombre' => $data['nombre'], 'docente_id' => $docente->id],
                ['descripcion' => $data['descripcion']]
            );

            Nota::firstOrCreate(
                ['estudiante_id' => $estudiante->id, 'materia_id' => $materia->id, 'periodo_id' => $periodo->id],
                [
                    'calificacion'   => fake()->randomFloat(2, 3, 5),
                    'registrado_por' => $docente->id,
                ]
            );
        }

        $acudiente->estudiantesAsociados()->syncWithoutDetaching([$estudiante->id]);

        $this->command->info('✅ Datos demo creados: 1 período, 5 materias, notas, representante→estudiante');
    }
}
