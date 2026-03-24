<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificacionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Generar roles
        $this->artisan('db:seed', ['--class' => 'AdminUserSeeder']);
    }

    public function test_can_list_notifications()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAsJwt($user)->getJson('/api/v1/notificaciones');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function test_can_create_notification()
    {
        $user = User::factory()->create();

        $payload = [
            'titulo' => 'Nueva Notificación',
            'mensaje' => 'Este es un mensaje de prueba',
            'tipo' => 'info',
            'canal' => 'Sistema',
            'id_usuario' => $user->id,
        ];

        $response = $this->actingAsJwt($user)->postJson('/api/v1/notificaciones', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'titulo',
                         'mensaje',
                         'tipo',
                         'canal'
                     ]
                 ]);

        // Note: Check the database table name. Assuming it's notificaciones.
        $this->assertDatabaseHas('notificaciones', [
            'titulo' => 'Nueva Notificación'
        ]);
    }

    public function test_can_show_notification()
    {
        $user = User::factory()->create();

        $payload = [
            'titulo' => 'Notificacion para mostrar',
            'mensaje' => 'Contenido',
            'tipo' => 'success',
            'canal' => 'Email',
            'id_usuario' => $user->id,
        ];

        $createResponse = $this->actingAsJwt($user)->postJson('/api/v1/notificaciones', $payload);
        $notificacionId = $createResponse->json('data.id');

        $response = $this->actingAsJwt($user)->getJson('/api/v1/notificaciones/' . $notificacionId);

        $response->assertStatus(200)
                 ->assertJsonPath('data.titulo', 'Notificacion para mostrar');
    }

    public function test_can_update_notification()
    {
        $user = User::factory()->create();

        $payload = [
            'titulo' => 'Notificacion original',
            'mensaje' => 'Contenido original',
            'tipo' => 'warning',
            'canal' => 'SMS',
            'id_usuario' => $user->id,
        ];

        $createResponse = $this->actingAsJwt($user)->postJson('/api/v1/notificaciones', $payload);
        $notificacionId = $createResponse->json('data.id');

        $updatePayload = [
            'titulo' => 'Notificacion actualizada',
            'mensaje' => 'Contenido actualizado',
            'tipo' => 'danger',
            'canal' => 'WhatsApp',
        ];

        $response = $this->actingAsJwt($user)->putJson('/api/v1/notificaciones/' . $notificacionId, $updatePayload);

        $response->assertStatus(200)
                 ->assertJsonPath('data.titulo', 'Notificacion actualizada');

        $this->assertDatabaseHas('notificaciones', [
            'titulo' => 'Notificacion actualizada'
        ]);
    }

    public function test_can_delete_notification()
    {
        $user = User::factory()->create();

        $payload = [
            'titulo' => 'Notificacion para borrar',
            'mensaje' => 'A borrar',
            'tipo' => 'info',
            'canal' => 'Sistema',
            'id_usuario' => $user->id,
        ];

        $createResponse = $this->actingAsJwt($user)->postJson('/api/v1/notificaciones', $payload);
        $notificacionId = $createResponse->json('data.id');

        $response = $this->actingAsJwt($user)->deleteJson('/api/v1/notificaciones/' . $notificacionId);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('notificaciones', [
            'titulo' => 'Notificacion para borrar' // Si usa soft deletes o no, esto verifica la vista básica
        ]);
    }
}
