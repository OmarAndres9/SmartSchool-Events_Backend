<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\EventoArchivo;
use App\Models\Eventos;
use App\Models\EventoValoracion;
use App\Models\Materia;
use App\Models\Nota;
use App\Models\Notificaciones;
use App\Models\Periodo;
use App\Models\Recursos;
use App\Models\Reporte;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@smartschool.com')->first();
        $organizador = User::where('email', 'organizador@smartschool.com')->first();
        $docente = User::where('email', 'docente@smartschool.com')->first();
        $estudiante = User::where('email', 'estudiante@smartschool.com')->first();
        $acudiente = User::where('email', 'acudiente@smartschool.com')->first();

        if (! $admin || ! $organizador || ! $docente || ! $estudiante || ! $acudiente) {
            $this->command->warn('Ejecutá primero AdminUserSeeder.');
            return;
        }

        // ========================================================================
        // 1. PERIODOS ACADÉMICOS
        // ========================================================================
        $periodosData = [
            ['nombre' => 'Semestre 2025-2', 'fecha_inicio' => '2025-08-01', 'fecha_fin' => '2025-12-15', 'activo' => false],
            ['nombre' => 'Semestre 2026-1', 'fecha_inicio' => '2026-02-01', 'fecha_fin' => '2026-06-30', 'activo' => true],
            ['nombre' => 'Semestre 2026-2', 'fecha_inicio' => '2026-08-01', 'fecha_fin' => '2026-12-15', 'activo' => false],
        ];
        $periodos = [];
        foreach ($periodosData as $data) {
            $periodos[] = Periodo::firstOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }
        $periodo2026_1 = $periodos[1];
        $periodo2025_2 = $periodos[0];
        $periodo2026_2 = $periodos[2];

        $this->command->info('✅ Períodos académicos creados.');

        // ========================================================================
        // 2. USUARIOS ADICIONALES
        // ========================================================================
        // Docentes
        $docentesData = [
            ['name' => 'María García',      'email' => 'maria.garcia@smartschool.com',   'documento' => '1000100001'],
            ['name' => 'Carlos López',      'email' => 'carlos.lopez@smartschool.com',   'documento' => '1000100002'],
            ['name' => 'Ana Martínez',      'email' => 'ana.martinez@smartschool.com',   'documento' => '1000100003'],
        ];
        $docentes = [$docente];
        foreach ($docentesData as $data) {
            $u = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('Docente2026*'),
                    'tipo_documento' => 'CC',
                    'documento' => $data['documento'],
                ]
            );
            $u->syncRoles(['docente']);
            $docentes[] = $u;
        }

        // Estudiantes
        $estudiantesData = [
            ['name' => 'Sofía Rodríguez',   'email' => 'sofia.rodriguez@smartschool.com',  'documento' => '2000100001', 'tipo_doc' => 'TI'],
            ['name' => 'Mateo González',    'email' => 'mateo.gonzalez@smartschool.com',  'documento' => '2000100002', 'tipo_doc' => 'TI'],
            ['name' => 'Valentina Pérez',   'email' => 'valentina.perez@smartschool.com', 'documento' => '2000100003', 'tipo_doc' => 'TI'],
            ['name' => 'Samuel Díaz',       'email' => 'samuel.diaz@smartschool.com',     'documento' => '2000100004', 'tipo_doc' => 'TI'],
            ['name' => 'Camila Torres',     'email' => 'camila.torres@smartschool.com',   'documento' => '2000100005', 'tipo_doc' => 'TI'],
        ];
        $estudiantes = [$estudiante];
        foreach ($estudiantesData as $data) {
            $u = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('Estud2026*'),
                    'tipo_documento' => $data['tipo_doc'],
                    'documento' => $data['documento'],
                ]
            );
            $u->syncRoles(['estudiante']);
            $estudiantes[] = $u;
        }

        // Acudientes
        $acudientesData = [
            ['name' => 'Laura Rodríguez',   'email' => 'laura.rodriguez@smartschool.com',  'documento' => '3000100001'],
            ['name' => 'Pedro González',    'email' => 'pedro.gonzalez@smartschool.com',  'documento' => '3000100002'],
            ['name' => 'Diana Pérez',       'email' => 'diana.perez@smartschool.com',     'documento' => '3000100003'],
        ];
        $acudientes = [$acudiente];
        foreach ($acudientesData as $data) {
            $u = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('Acud2026*'),
                    'tipo_documento' => 'CC',
                    'documento' => $data['documento'],
                ]
            );
            $u->syncRoles(['acudiente']);
            $acudientes[] = $u;
        }

        $this->command->info('✅ Usuarios adicionales creados: ' . (count($docentes) + count($estudiantes) + count($acudientes)) . ' total.');

        // ========================================================================
        // 3. MATERIAS
        // ========================================================================
        $materiasData = [
            ['nombre' => 'Matemáticas',        'descripcion' => 'Álgebra, geometría y trigonometría',           'docente_id' => $docente->id],
            ['nombre' => 'Lenguaje',            'descripcion' => 'Lengua castellana y literatura',               'docente_id' => $docente->id],
            ['nombre' => 'Ciencias Naturales',  'descripcion' => 'Biología y química básica',                    'docente_id' => $docente->id],
            ['nombre' => 'Inglés',              'descripcion' => 'Inglés nivel A2',                             'docente_id' => $docentes[1]->id],
            ['nombre' => 'Historia',            'descripcion' => 'Historia universal y de Colombia',             'docente_id' => $docentes[1]->id],
            ['nombre' => 'Física',              'descripcion' => 'Física mecánica y ondas',                     'docente_id' => $docentes[2]->id],
            ['nombre' => 'Química',             'descripcion' => 'Química general y orgánica',                  'docente_id' => $docentes[2]->id],
            ['nombre' => 'Educación Física',    'descripcion' => 'Deportes y actividad física',                  'docente_id' => $docentes[3]->id],
            ['nombre' => 'Tecnología',          'descripcion' => 'Informática y pensamiento computacional',      'docente_id' => $docentes[3]->id],
        ];
        $materias = [];
        foreach ($materiasData as $data) {
            $materias[] = Materia::firstOrCreate(
                ['nombre' => $data['nombre'], 'docente_id' => $data['docente_id']],
                ['descripcion' => $data['descripcion']]
            );
        }

        $this->command->info('✅ Materias creadas: ' . count($materias));

        // ========================================================================
        // 4. NOTAS (calificaciones)
        // ========================================================================
        $notasCreadas = 0;
        foreach ($estudiantes as $est) {
            foreach ($materias as $mat) {
                foreach ([$periodo2025_2, $periodo2026_1] as $per) {
                    $calificacion = match (true) {
                        $est->id === $estudiante->id && $mat->nombre === 'Matemáticas' => fake()->randomFloat(2, 3.5, 5),
                        $est->id === $estudiante->id && $mat->nombre === 'Inglés' => fake()->randomFloat(2, 2, 3.5),
                        default => fake()->randomFloat(2, 2, 5),
                    };
                    Nota::firstOrCreate(
                        ['estudiante_id' => $est->id, 'materia_id' => $mat->id, 'periodo_id' => $per->id],
                        [
                            'calificacion' => $calificacion,
                            'registrado_por' => $mat->docente_id,
                        ]
                    );
                    $notasCreadas++;
                }
            }
        }

        $this->command->info("✅ Notas creadas: {$notasCreadas}");

        // ========================================================================
        // 5. REPRESENTANTE → ESTUDIANTES
        // ========================================================================
        $acudientes[0]->estudiantesAsociados()->syncWithoutDetaching([
            $estudiantes[0]->id, $estudiantes[1]->id,
        ]);
        $acudientes[1]->estudiantesAsociados()->syncWithoutDetaching([
            $estudiantes[2]->id, $estudiantes[3]->id,
        ]);
        $acudientes[2]->estudiantesAsociados()->syncWithoutDetaching([
            $estudiantes[4]->id,
        ]);
        // El acudiente demo ya ve al estudiante demo
        $acudientes[0]->estudiantesAsociados()->syncWithoutDetaching([$estudiante->id]);

        $this->command->info('✅ Representantes vinculados a estudiantes.');

        // ========================================================================
        // 6. RECURSOS
        // ========================================================================
        $recursosData = [
            ['nombre' => 'Auditorio Principal',  'tipo' => 'auditorio',     'ubicacion' => 'Bloque A', 'capacidad' => 300, 'estado' => 'disponible',  'descripcion' => 'Auditorio con sonido profesional y video proyector'],
            ['nombre' => 'Salón 101',            'tipo' => 'salon',         'ubicacion' => 'Bloque A', 'capacidad' => 40,  'estado' => 'disponible',  'descripcion' => 'Salón con tablero digital'],
            ['nombre' => 'Salón 102',            'tipo' => 'salon',         'ubicacion' => 'Bloque A', 'capacidad' => 35,  'estado' => 'mantenimiento', 'descripcion' => 'En reparación'],
            ['nombre' => 'Laboratorio Química',  'tipo' => 'laboratorio',   'ubicacion' => 'Bloque B', 'capacidad' => 25,  'estado' => 'disponible',  'descripcion' => 'Equipos de laboratorio completos'],
            ['nombre' => 'Laboratorio Física',   'tipo' => 'laboratorio',   'ubicacion' => 'Bloque B', 'capacidad' => 25,  'estado' => 'ocupado',     'descripcion' => 'Uso exclusivo del departamento de ciencias'],
            ['nombre' => 'Cancha de Fútbol',     'tipo' => 'cancha',        'ubicacion' => 'Área exterior', 'capacidad' => 200, 'estado' => 'disponible', 'descripcion' => 'Cancha sintética iluminada'],
            ['nombre' => 'Cancha de Baloncesto', 'tipo' => 'cancha',        'ubicacion' => 'Área exterior', 'capacidad' => 100, 'estado' => 'disponible', 'descripcion' => null],
            ['nombre' => 'Biblioteca',           'tipo' => 'biblioteca',    'ubicacion' => 'Bloque C', 'capacidad' => 80,  'estado' => 'disponible',  'descripcion' => 'Biblioteca con sala de lectura y computadores'],
            ['nombre' => 'Sala de Cómputo',      'tipo' => 'salon_computo', 'ubicacion' => 'Bloque C', 'capacidad' => 30,  'estado' => 'disponible',  'descripcion' => '30 equipos con software educativo'],
            ['nombre' => 'Salón de Actos',        'tipo' => 'auditorio',    'ubicacion' => 'Bloque A', 'capacidad' => 150, 'estado' => 'disponible',  'descripcion' => 'Escenario con luces y sonido'],
            ['nombre' => 'Aula Virtual Zoom',     'tipo' => 'virtual',      'ubicacion' => 'Virtual',  'capacidad' => 100, 'estado' => 'disponible',  'descripcion' => 'Licencia Zoom Education'],
            ['nombre' => 'Salón Música',          'tipo' => 'salon',        'ubicacion' => 'Bloque B', 'capacidad' => 30,  'estado' => 'disponible',  'descripcion' => 'Instrumentos musicales y equipo de sonido'],
        ];
        $recursos = [];
        foreach ($recursosData as $data) {
            $recursos[] = Recursos::firstOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }

        $this->command->info('✅ Recursos creados: ' . count($recursos));

        // ========================================================================
        // 7. EVENTOS
        // ========================================================================
        $eventosData = [
            [
                'nombre' => 'Taller de Liderazgo Estudiantil',
                'descripcion' => 'Taller intensivo para desarrollar habilidades de liderazgo en los estudiantes de secundaria. Se abordarán temas como comunicación asertiva, trabajo en equipo y resolución de conflictos.',
                'fecha_inicio' => '2026-03-15 08:00:00',
                'fecha_fin' => '2026-03-15 12:00:00',
                'lugar' => 'Auditorio Principal',
                'tipo_evento' => 'taller',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Secundaria',
                'creado_por' => $organizador->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Conferencia: Inteligencia Artificial en la Educación',
                'descripcion' => 'Conferencia magistral sobre el impacto de la IA en los procesos educativos modernos. Invitado especial: Dr. Roberto Méndez.',
                'fecha_inicio' => '2026-04-10 10:00:00',
                'fecha_fin' => '2026-04-10 12:30:00',
                'lugar' => 'Auditorio Principal',
                'tipo_evento' => 'conferencia',
                'modalidad' => 'mixto',
                'grupo_destinado' => 'Todos',
                'creado_por' => $admin->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Reunión de Padres de Familia - Primer Semestre',
                'descripcion' => 'Reunión informativa para padres de familia sobre el rendimiento académico y actividades del primer semestre.',
                'fecha_inicio' => '2026-05-05 14:00:00',
                'fecha_fin' => '2026-05-05 16:00:00',
                'lugar' => 'Salón de Actos',
                'tipo_evento' => 'reunion',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Padres de Familia',
                'creado_por' => $docente->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Feria Cultural 2026',
                'descripcion' => 'Feria cultural con presentaciones artísticas, stands de comida típica y muestras folclóricas de diferentes regiones.',
                'fecha_inicio' => '2026-05-20 09:00:00',
                'fecha_fin' => '2026-05-20 18:00:00',
                'lugar' => 'Patio Central',
                'tipo_evento' => 'cultural',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Todos',
                'creado_por' => $organizador->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Campeonato Intercursos de Fútbol',
                'descripcion' => 'Torneo deportivo entre los diferentes cursos. Partidos todos los viernes durante el semestre.',
                'fecha_inicio' => '2026-03-01 14:00:00',
                'fecha_fin' => '2026-06-30 16:00:00',
                'lugar' => 'Cancha de Fútbol',
                'tipo_evento' => 'deportivo',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Estudiantes',
                'creado_por' => $docentes[3]->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Taller de Poesía y Narrativa',
                'descripcion' => 'Taller literario abierto a todos los estudiantes interesados en la escritura creativa.',
                'fecha_inicio' => '2026-04-22 15:00:00',
                'fecha_fin' => '2026-04-22 17:00:00',
                'lugar' => 'Biblioteca',
                'tipo_evento' => 'taller',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Estudiantes',
                'creado_por' => $docente->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Olimpiadas de Matemáticas',
                'descripcion' => 'Competencia de resolución de problemas matemáticos por niveles. Participan estudiantes de todos los grados.',
                'fecha_inicio' => '2026-05-30 08:00:00',
                'fecha_fin' => '2026-05-30 13:00:00',
                'lugar' => 'Salón 101',
                'tipo_evento' => 'academico',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Estudiantes',
                'creado_por' => $docente->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Charla: Prevención del Bullying',
                'descripcion' => 'Charla educativa sobre prevención del acoso escolar, dictada por el departamento de psicología.',
                'fecha_inicio' => '2026-03-25 09:00:00',
                'fecha_fin' => '2026-03-25 11:00:00',
                'lugar' => 'Salón de Actos',
                'tipo_evento' => 'conferencia',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Todos',
                'creado_por' => $admin->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Evento Privado: Junta Directiva',
                'descripcion' => 'Reunión mensual de la junta directiva del colegio.',
                'fecha_inicio' => '2026-03-20 10:00:00',
                'fecha_fin' => '2026-03-20 12:00:00',
                'lugar' => 'Sala de Juntas',
                'tipo_evento' => 'reunion',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Directivos',
                'creado_por' => $admin->id,
                'visibilidad' => 'privado',
            ],
            [
                'nombre' => 'Curso Virtual de Inglés Avanzado',
                'descripcion' => 'Curso intensivo de inglés nivel B1. Sesiones virtuales dos veces por semana.',
                'fecha_inicio' => '2026-04-01 18:00:00',
                'fecha_fin' => '2026-06-30 20:00:00',
                'lugar' => null,
                'tipo_evento' => 'taller',
                'modalidad' => 'virtual',
                'grupo_destinado' => 'Estudiantes',
                'creado_por' => $docentes[1]->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Festival de la Canción',
                'descripcion' => 'Concurso de canto intercolegial con premiación a los tres primeros lugares.',
                'fecha_inicio' => '2026-06-15 14:00:00',
                'fecha_fin' => '2026-06-15 20:00:00',
                'lugar' => 'Auditorio Principal',
                'tipo_evento' => 'cultural',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Todos',
                'creado_por' => $organizador->id,
                'visibilidad' => 'publico',
            ],
            [
                'nombre' => 'Feria de la Ciencia',
                'descripcion' => 'Exposición de proyectos científicos realizados por los estudiantes de todos los niveles.',
                'fecha_inicio' => '2026-04-29 08:00:00',
                'fecha_fin' => '2026-04-29 15:00:00',
                'lugar' => 'Laboratorio Química',
                'tipo_evento' => 'academico',
                'modalidad' => 'presencial',
                'grupo_destinado' => 'Todos',
                'creado_por' => $docentes[2]->id,
                'visibilidad' => 'publico',
            ],
        ];
        $eventos = [];
        foreach ($eventosData as $data) {
            $eventos[] = Eventos::firstOrCreate(
                ['nombre' => $data['nombre'], 'fecha_inicio' => $data['fecha_inicio']],
                $data
            );
        }

        $this->command->info('✅ Eventos creados: ' . count($eventos));

        // ========================================================================
        // 8. EVENTO → RECURSOS (asignación)
        // ========================================================================
        $asignaciones = [
            ['evento_idx' => 0, 'recurso_idx' => 0, 'cantidad' => 1],
            ['evento_idx' => 1, 'recurso_idx' => 0, 'cantidad' => 1],
            ['evento_idx' => 1, 'recurso_idx' => 10, 'cantidad' => 1],
            ['evento_idx' => 2, 'recurso_idx' => 9, 'cantidad' => 1],
            ['evento_idx' => 3, 'recurso_idx' => 0, 'cantidad' => 1],
            ['evento_idx' => 3, 'recurso_idx' => 5, 'cantidad' => 1],
            ['evento_idx' => 4, 'recurso_idx' => 5, 'cantidad' => 1],
            ['evento_idx' => 5, 'recurso_idx' => 7, 'cantidad' => 1],
            ['evento_idx' => 6, 'recurso_idx' => 1, 'cantidad' => 1],
            ['evento_idx' => 7, 'recurso_idx' => 9, 'cantidad' => 1],
            ['evento_idx' => 8, 'recurso_idx' => 1, 'cantidad' => 1],
            ['evento_idx' => 9, 'recurso_idx' => 10, 'cantidad' => 2],
            ['evento_idx' => 10, 'recurso_idx' => 0, 'cantidad' => 1],
            ['evento_idx' => 11, 'recurso_idx' => 3, 'cantidad' => 1],
        ];
        foreach ($asignaciones as $asig) {
            $eventos[$asig['evento_idx']]->recursos()->syncWithoutDetaching([
                $recursos[$asig['recurso_idx']]->id => ['cantidad' => $asig['cantidad']],
            ]);
        }

        $this->command->info('✅ Recursos asignados a eventos.');

        // ========================================================================
        // 9. INSCRIPCIONES A EVENTOS
        // ========================================================================
        $inscripcionesEventos = [
            0 => [0, 1, 3, 5],          // estudiantes index
            1 => [0, 1, 2, 3, 4, 5],    // todos
            2 => [0, 3],                 // acudientes
            4 => [0, 1, 2, 3, 4, 5],    // todos
            5 => [0, 2, 4],
            6 => [0, 1, 3, 5],
            9 => [1, 2, 4],
        ];
        foreach ($inscripcionesEventos as $eventoIdx => $estIdxs) {
            $evento = $eventos[$eventoIdx];
            foreach ($estIdxs as $estIdx) {
                $evento->inscripciones()->syncWithoutDetaching([
                    $estudiantes[$estIdx]->id => [
                        'estado' => fake()->randomElement(['pendiente', 'confirmada', 'asistio', 'cancelada']),
                    ],
                ]);
            }
        }

        // El organizador y acudiente se inscriben a algunos eventos
        $eventos[1]->inscripciones()->syncWithoutDetaching([$organizador->id => ['estado' => 'confirmada']]);
        $eventos[2]->inscripciones()->syncWithoutDetaching([$acudiente->id => ['estado' => 'confirmada']]);
        $eventos[3]->inscripciones()->syncWithoutDetaching([$acudientes[0]->id => ['estado' => 'pendiente']]);

        $this->command->info('✅ Inscripciones creadas.');

        // ========================================================================
        // 10. VALORACIONES (rating de eventos)
        // ========================================================================
        $valoracionesEventos = [0, 1, 3, 5, 6, 10, 11];
        foreach ($valoracionesEventos as $eventoIdx) {
            $evento = $eventos[$eventoIdx];
            $inscritos = $evento->inscripciones;
            foreach ($inscritos as $user) {
                if (fake()->boolean(70)) {
                    EventoValoracion::firstOrCreate(
                        ['evento_id' => $evento->id, 'user_id' => $user->id],
                        [
                            'puntuacion' => fake()->numberBetween(3, 5),
                            'comentario' => fake()->optional(60)->randomElement([
                                'Excelente evento', 'Muy bien organizado',
                                'Me gustó mucho', 'Buena experiencia',
                                'Podría mejorar la logística', 'Gran aprendizaje',
                                'Superó mis expectativas', 'Interesante temática',
                            ]),
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ Valoraciones creadas.');

        // ========================================================================
        // 11. FAVORITOS
        // ========================================================================
        $favoritos = [
            0 => [0, 1, 5],
            1 => [0, 2, 3],
            3 => [1, 4],
            10 => [0, 5],
        ];
        foreach ($favoritos as $eventoIdx => $userIdxs) {
            foreach ($userIdxs as $userIdx) {
                $eventos[$eventoIdx]->favoritos()->syncWithoutDetaching([
                    $estudiantes[$userIdx]->id,
                ]);
            }
        }

        $this->command->info('✅ Favoritos creados.');

        // ========================================================================
        // 12. ARCHIVOS DE EVENTOS
        // ========================================================================
        $archivosData = [
            ['evento_idx' => 0, 'nombre_original' => 'taller_liderazgo.pdf',       'tipo' => 'application/pdf',     'tamano' => 245000],
            ['evento_idx' => 0, 'nombre_original' => 'presentacion_liderazgo.pptx','tipo' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'tamano' => 1800000],
            ['evento_idx' => 1, 'nombre_original' => 'ia_educacion_invitation.pdf', 'tipo' => 'application/pdf',     'tamano' => 320000],
            ['evento_idx' => 3, 'nombre_original' => 'cartel_feria_cultural.jpg',   'tipo' => 'image/jpeg',          'tamano' => 4500000],
            ['evento_idx' => 6, 'nombre_original' => 'olimpiadas_ejercicios.pdf',   'tipo' => 'application/pdf',     'tamano' => 890000],
            ['evento_idx' => 11, 'nombre_original' => 'proyectos_ciencia.pdf',      'tipo' => 'application/pdf',     'tamano' => 1200000],
        ];
        foreach ($archivosData as $data) {
            EventoArchivo::firstOrCreate(
                ['nombre_original' => $data['nombre_original'], 'evento_id' => $eventos[$data['evento_idx']]->id],
                [
                    'evento_id' => $eventos[$data['evento_idx']]->id,
                    'nombre_original' => $data['nombre_original'],
                    'ruta' => 'eventos/' . fake()->uuid() . '_' . $data['nombre_original'],
                    'tipo' => $data['tipo'],
                    'tamano' => $data['tamano'],
                ]
            );
        }

        $this->command->info('✅ Archivos de eventos creados.');

        // ========================================================================
        // 13. NOTIFICACIONES
        // ========================================================================
        $notificacionesData = [
            ['titulo' => 'Bienvenido al nuevo semestre', 'mensaje' => 'Esperamos que tengas un excelente semestre académico.', 'tipo' => 'informacion', 'canal' => 'notificacion', 'id_usuario' => $estudiante->id, 'id_evento' => null],
            ['titulo' => 'Recordatorio: Taller de Liderazgo', 'mensaje' => 'No olvides asistir al taller de liderazgo este sábado.', 'tipo' => 'recordatorio', 'canal' => 'ambos', 'id_usuario' => $estudiante->id, 'id_evento' => $eventos[0]->id],
            ['titulo' => 'Nuevo evento: Feria Cultural', 'mensaje' => 'Se ha creado un nuevo evento: Feria Cultural 2026. ¡Participa!', 'tipo' => 'informacion', 'canal' => 'notificacion', 'id_usuario' => $estudiante->id, 'id_evento' => $eventos[3]->id],
            ['titulo' => 'Inscripción confirmada', 'mensaje' => 'Tu inscripción al Campeonato Intercursos de Fútbol ha sido confirmada.', 'tipo' => 'confirmacion', 'canal' => 'email', 'id_usuario' => $estudiantes[1]->id, 'id_evento' => $eventos[4]->id],
            ['titulo' => 'Cambio de horario: Reunión de Padres', 'mensaje' => 'La reunión de padres se ha cambiado a las 2:00 PM.', 'tipo' => 'alerta', 'canal' => 'ambos', 'id_usuario' => $acudiente->id, 'id_evento' => $eventos[2]->id],
            ['titulo' => 'Nueva calificación disponible', 'mensaje' => 'Tu calificación de Matemáticas ya está disponible en el sistema.', 'tipo' => 'informacion', 'canal' => 'notificacion', 'id_usuario' => $estudiantes[2]->id, 'id_evento' => null],
            ['titulo' => 'Evento próximo: Feria de la Ciencia', 'mensaje' => 'La Feria de la Ciencia se realizará este viernes. ¡Prepara tu proyecto!', 'tipo' => 'recordatorio', 'canal' => 'ambos', 'id_usuario' => $estudiantes[3]->id, 'id_evento' => $eventos[11]->id],
            ['titulo' => 'Recordatorio para todos los estudiantes', 'mensaje' => 'Se acerca la fecha límite de inscripciones para las Olimpiadas de Matemáticas.', 'tipo' => 'recordatorio', 'canal' => 'notificacion', 'id_usuario' => $admin->id, 'id_evento' => $eventos[6]->id],
        ];
        foreach ($notificacionesData as $data) {
            Notificaciones::firstOrCreate(
                ['titulo' => $data['titulo'], 'id_usuario' => $data['id_usuario']],
                [
                    'mensaje' => $data['mensaje'],
                    'tipo' => $data['tipo'],
                    'canal' => $data['canal'],
                    'fecha_creacion' => fake()->dateTimeBetween('-2 weeks', 'now'),
                    'id_usuario' => $data['id_usuario'],
                    'id_evento' => $data['id_evento'],
                ]
            );
        }

        $this->command->info('✅ Notificaciones creadas.');

        // ========================================================================
        // 14. REPORTES
        // ========================================================================
        $reportesData = [
            ['tipo' => 'asistencia',  'descripcion' => 'Reporte de asistencia al Taller de Liderazgo. Asistieron 25 de 30 inscritos.',                                              'fecha' => '2026-03-15 13:00:00', 'estado' => 'aprobado',  'id_usuario' => $organizador->id, 'id_evento' => $eventos[0]->id],
            ['tipo' => 'evento',      'descripcion' => 'Reporte post-evento de la Conferencia de IA. Evaluación general: 4.5/5.',                                                     'fecha' => '2026-04-10 14:00:00', 'estado' => 'aprobado',  'id_usuario' => $admin->id,       'id_evento' => $eventos[1]->id],
            ['tipo' => 'rendimiento', 'descripcion' => 'Reporte de rendimiento académico del primer semestre. Promedio general: 3.8.',                                                'fecha' => '2026-06-30 17:00:00', 'estado' => 'pendiente', 'id_usuario' => $docente->id,    'id_evento' => null],
            ['tipo' => 'incidente',   'descripcion' => 'Se reportó daño en el equipo de sonido del Auditorio Principal durante el Festival de la Canción.',                          'fecha' => '2026-06-15 21:00:00', 'estado' => 'pendiente', 'id_usuario' => $organizador->id, 'id_evento' => $eventos[10]->id],
            ['tipo' => 'general',     'descripcion' => 'Informe general de actividades culturales del primer semestre.',                                                               'fecha' => '2026-06-30 16:00:00', 'estado' => 'aprobado',  'id_usuario' => $admin->id,       'id_evento' => null],
            ['tipo' => 'asistencia',  'descripcion' => 'Reporte de asistencia a la Reunión de Padres. Asistieron 45 padres.',                                                         'fecha' => '2026-05-05 17:00:00', 'estado' => 'aprobado',  'id_usuario' => $docente->id,    'id_evento' => $eventos[2]->id],
            ['tipo' => 'incidente',   'descripcion' => 'Comportamiento inadecuado de un estudiante durante la Feria Cultural.',                                                       'fecha' => '2026-05-20 18:30:00', 'estado' => 'rechazado','id_usuario' => $docentes[1]->id,  'id_evento' => $eventos[3]->id],
        ];
        foreach ($reportesData as $data) {
            Reporte::firstOrCreate(
                ['descripcion' => $data['descripcion']],
                $data
            );
        }

        $this->command->info('✅ Reportes creados.');

        // ========================================================================
        // 15. CITAS
        // ========================================================================
        $citasData = [
            ['solicitante_id' => $acudiente->id, 'destinatario_id' => $docente->id,    'fecha_solicitada' => '2026-03-10 15:00:00', 'motivo' => 'Entrega de notas',            'comentario' => 'Quisiera revisar las notas de mi hijo.', 'estado' => 'aprobada'],
            ['solicitante_id' => $acudiente->id, 'destinatario_id' => $docente->id,    'fecha_solicitada' => '2026-05-15 10:00:00', 'motivo' => 'Problema académico',           'comentario' => 'Mi hijo está teniendo dificultades en matemáticas.', 'estado' => 'pendiente'],
            ['solicitante_id' => $acudientes[0]->id, 'destinatario_id' => $docentes[1]->id, 'fecha_solicitada' => '2026-04-20 14:00:00', 'motivo' => 'Solicitud de reunión',     'comentario' => null, 'estado' => 'aprobada'],
            ['solicitante_id' => $estudiante->id, 'destinatario_id' => $docente->id,  'fecha_solicitada' => '2026-05-25 11:00:00', 'motivo' => 'Orientación vocacional',      'comentario' => 'Necesito ayuda para elegir mi énfasis.', 'estado' => 'pendiente'],
            ['solicitante_id' => $estudiantes[1]->id, 'destinatario_id' => $docentes[2]->id, 'fecha_solicitada' => '2026-06-05 09:00:00', 'motivo' => 'Queja o sugerencia',       'comentario' => 'El laboratorio de física necesita mejores equipos.', 'estado' => 'completada'],
            ['solicitante_id' => $acudientes[1]->id, 'destinatario_id' => $admin->id, 'fecha_solicitada' => '2026-03-30 16:00:00', 'motivo' => 'Seguimiento académico',       'comentario' => 'Quiero saber cómo va mi hija en todas las materias.', 'estado' => 'aprobada'],
            ['solicitante_id' => $acudientes[2]->id, 'destinatario_id' => $docentes[3]->id, 'fecha_solicitada' => '2026-04-15 08:00:00', 'motivo' => 'Otros',                    'comentario' => 'Consulta sobre horarios deportivos.', 'estado' => 'rechazada'],
        ];
        foreach ($citasData as $data) {
            Cita::firstOrCreate(
                ['solicitante_id' => $data['solicitante_id'], 'fecha_solicitada' => $data['fecha_solicitada'], 'motivo' => $data['motivo']],
                $data
            );
        }

        $this->command->info('✅ Citas creadas.');

        // ========================================================================
        // RESUMEN FINAL
        // ========================================================================
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════╗');
        $this->command->info('║    TEST DATA SEEDER COMPLETADO ✅       ║');
        $this->command->info('╠══════════════════════════════════════════╣');
        $this->command->info('║  Usuarios:       ' . str_pad(User::count(), 14) . '  ║');
        $this->command->info('║  Períodos:       ' . str_pad(Periodo::count(), 14) . '  ║');
        $this->command->info('║  Materias:       ' . str_pad(Materia::count(), 14) . '  ║');
        $this->command->info('║  Notas:          ' . str_pad(Nota::count(), 14) . '  ║');
        $this->command->info('║  Eventos:        ' . str_pad(Eventos::count(), 14) . '  ║');
        $this->command->info('║  Recursos:       ' . str_pad(Recursos::count(), 14) . '  ║');
        $this->command->info('║  Valoraciones:   ' . str_pad(EventoValoracion::count(), 14) . '  ║');
        $this->command->info('║  Archivos:       ' . str_pad(EventoArchivo::count(), 14) . '  ║');
        $this->command->info('║  Notificaciones: ' . str_pad(Notificaciones::count(), 14) . '  ║');
        $this->command->info('║  Reportes:       ' . str_pad(Reporte::count(), 14) . '  ║');
        $this->command->info('║  Citas:          ' . str_pad(Cita::count(), 14) . '  ║');
        $this->command->info('╚══════════════════════════════════════════╝');
        $this->command->info('');
    }
}
