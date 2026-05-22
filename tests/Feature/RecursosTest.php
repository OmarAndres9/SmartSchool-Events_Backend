<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecursosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'AdminUserSeeder']);
    }

    private function getOrganizador()
    {
        return User::where('email', 'organizador@smartschool.com')->first();
    }

    private function getEstudiante()
    {
        return User::where('email', 'estudiante@smartschool.com')->first();
    }

    public function test_organizador_can_list_recursos()
    {
        $response = $this->actingAsJwt($this->getOrganizador())->getJson('/api/v1/recursos');
        $response->assertStatus(200);
    }

    public function test_estudiante_cannot_create_recurso()
    {
        $response = $this->actingAsJwt($this->getEstudiante())->postJson('/api/v1/recursos', [
            'nombre' => 'Test',
            'ubicacion' => 'Sala 1',
            'estado' => 'disponible',
        ]);
        $response->assertStatus(403);
    }

    public function test_organizador_can_create_recurso()
    {
        $response = $this->actingAsJwt($this->getOrganizador())->postJson('/api/v1/recursos', [
            'nombre' => 'Proyector',
            'ubicacion' => 'Sala 101',
            'estado' => 'disponible',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.nombre', 'Proyector');
        $this->assertDatabaseHas('_recursos__table', ['nombre' => 'Proyector']);
    }

    public function test_organizador_can_show_recurso()
    {
        $create = $this->actingAsJwt($this->getOrganizador())->postJson('/api/v1/recursos', [
            'nombre' => 'Pantalla',
            'ubicacion' => 'Auditorio',
            'estado' => 'disponible',
        ]);
        $id = $create->json('data.id');

        $response = $this->actingAsJwt($this->getOrganizador())->getJson("/api/v1/recursos/{$id}");
        $response->assertStatus(200)->assertJsonPath('data.nombre', 'Pantalla');
    }

    public function test_organizador_can_update_recurso()
    {
        $create = $this->actingAsJwt($this->getOrganizador())->postJson('/api/v1/recursos', [
            'nombre' => 'Mesa',
            'ubicacion' => 'Bodega',
            'estado' => 'disponible',
        ]);
        $id = $create->json('data.id');

        $response = $this->actingAsJwt($this->getOrganizador())->putJson("/api/v1/recursos/{$id}", [
            'nombre' => 'Mesa grande',
            'ubicacion' => 'Bodega 2',
            'estado' => 'ocupado',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.nombre', 'Mesa grande');
    }

    public function test_organizador_can_delete_recurso()
    {
        $create = $this->actingAsJwt($this->getOrganizador())->postJson('/api/v1/recursos', [
            'nombre' => 'Silla',
            'ubicacion' => 'Sala',
            'estado' => 'disponible',
        ]);
        $id = $create->json('data.id');

        $response = $this->actingAsJwt($this->getOrganizador())->deleteJson("/api/v1/recursos/{$id}");
        $response->assertStatus(204);
    }
}
