<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Generar roles y usuario organizador
        $this->artisan('db:seed', ['--class' => 'AdminUserSeeder']);
    }

    private function getOrganizadorUser()
    {
        return User::where('email', 'organizador@smartschool.com')->first();
    }

    private function getEstudianteUser()
    {
        return User::where('email', 'estudiante@smartschool.com')->first();
    }

    // --- EVENTOS ---
    public function test_organizador_can_list_eventos()
    {
        $user = $this->getOrganizadorUser();

        $response = $this->actingAsJwt($user)->getJson('/api/v1/eventos');

        $response->assertStatus(200);
    }

    public function test_estudiante_cannot_create_evento()
    {
        $user = $this->getEstudianteUser();

        $response = $this->actingAsJwt($user)->postJson('/api/v1/eventos', [
            'nombre' => 'Evento Test',
            'fecha_inicio' => now()->addDays(1)->toDateString(),
            'tipo_evento' => 'Academico',
            'modalidad' => 'Presencial',
        ]);

        $response->assertStatus(403);
    }

    public function test_organizador_can_create_evento()
    {
        $user = $this->getOrganizadorUser();

        $payload = [
            'nombre' => 'Evento Test',
            'fecha_inicio' => now()->addDays(1)->toDateString(),
            'tipo_evento' => 'Academico',
            'modalidad' => 'Presencial',
        ];

        $response = $this->actingAsJwt($user)->postJson('/api/v1/eventos', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.nombre', 'Evento Test');

        $this->assertDatabaseHas('eventos', [
            'nombre' => 'Evento Test'
        ]);
    }

    public function test_organizador_can_update_evento()
    {
        $user = $this->getOrganizadorUser();

        $createResponse = $this->actingAsJwt($user)->postJson('/api/v1/eventos', [
            'nombre' => 'Evento Viejo',
            'fecha_inicio' => now()->addDays(1)->toDateString(),
            'tipo_evento' => 'Cultural',
            'modalidad' => 'Virtual',
        ]);
        
        $eventoId = $createResponse->json('data.id');

        $response = $this->actingAsJwt($user)->putJson("/api/v1/eventos/{$eventoId}", [
            'nombre' => 'Evento Nuevo',
            'fecha_inicio' => now()->addDays(2)->toDateString(),
            'tipo_evento' => 'Deportivo',
            'modalidad' => 'Virtual',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.nombre', 'Evento Nuevo');
    }

    public function test_organizador_can_delete_evento()
    {
        $user = $this->getOrganizadorUser();

        $createResponse = $this->actingAsJwt($user)->postJson('/api/v1/eventos', [
            'nombre' => 'Evento a borrar',
            'fecha_inicio' => now()->addDays(1)->toDateString(),
            'tipo_evento' => 'Cultural',
            'modalidad' => 'Virtual',
        ]);
        
        $eventoId = $createResponse->json('data.id');

        $response = $this->actingAsJwt($user)->deleteJson("/api/v1/eventos/{$eventoId}");

        $response->assertStatus(204);
        // Note: depends on soft deletes or hard deletes setup in the model
    }

    public function test_organizador_can_list_mis_eventos()
    {
        $user = $this->getOrganizadorUser();

        $this->actingAsJwt($user)->postJson('/api/v1/eventos', [
            'nombre' => 'Mi Evento Propio',
            'fecha_inicio' => now()->addDays(1)->toDateString(),
            'tipo_evento' => 'Cultural',
            'modalidad' => 'Virtual',
        ]);

        $response = $this->actingAsJwt($user)->getJson('/api/v1/eventos/mis-eventos');

        $response->assertStatus(200);
        // Validar que en la data devuelta haya al menos 1 elemento
        $this->assertNotEmpty($response->json('data'));
    }

    // --- RECURSOS ---
    public function test_organizador_can_create_recurso()
    {
        $user = $this->getOrganizadorUser();

        $payload = [
            'nombre' => 'Proyector',
            'ubicacion' => 'Sala 101',
            'estado' => 'disponible',
        ];

        $response = $this->actingAsJwt($user)->postJson('/api/v1/recursos', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.nombre', 'Proyector');

        $this->assertDatabaseHas('_recursos__table', [ // Assuming model uses this table or 'recursos'
            'nombre' => 'Proyector'
        ]);
    }

    // --- ASIGNACION RECURSOS-EVENTOS ---
    public function test_organizador_can_assign_recurso_to_evento()
    {
        $user = $this->getOrganizadorUser();

        // Crear Recurso
        $recursoResponse = $this->actingAsJwt($user)->postJson('/api/v1/recursos', [
            'nombre' => 'Sillas',
            'ubicacion' => 'Bodega',
            'estado' => 'disponible',
        ]);
        $recursoId = $recursoResponse->json('data.id');

        // Crear Evento
        $eventoResponse = $this->actingAsJwt($user)->postJson('/api/v1/eventos', [
            'nombre' => 'Evento con Sillas',
            'fecha_inicio' => now()->addDays(1)->toDateString(),
            'tipo_evento' => 'Cultural',
            'modalidad' => 'Presencial',
        ]);
        $eventoId = $eventoResponse->json('data.id');

        // Asignar
        $response = $this->actingAsJwt($user)->postJson("/api/v1/eventos/{$eventoId}/recursos", [
            'recurso_id' => $recursoId,
            'cantidad' => 50
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Recurso asignado al evento correctamente']);
    }

    public function test_organizador_can_unassign_recurso_from_evento()
    {
        $user = $this->getOrganizadorUser();

        // Crear Recurso
        $recursoResponse = $this->actingAsJwt($user)->postJson('/api/v1/recursos', [
            'nombre' => 'Mesas',
            'ubicacion' => 'Bodega',
            'estado' => 'disponible',
        ]);
        $recursoId = $recursoResponse->json('data.id');

        // Crear Evento
        $eventoResponse = $this->actingAsJwt($user)->postJson('/api/v1/eventos', [
            'nombre' => 'Evento con Mesas',
            'fecha_inicio' => now()->addDays(1)->toDateString(),
            'tipo_evento' => 'Cultural',
            'modalidad' => 'Presencial',
        ]);
        $eventoId = $eventoResponse->json('data.id');

        // Asignar
        $this->actingAsJwt($user)->postJson("/api/v1/eventos/{$eventoId}/recursos", [
            'recurso_id' => $recursoId,
            'cantidad' => 10
        ]);

        // Desasignar
        $response = $this->actingAsJwt($user)->deleteJson("/api/v1/eventos/{$eventoId}/recursos/{$recursoId}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Recurso desasignado del evento correctamente']);
    }
}
