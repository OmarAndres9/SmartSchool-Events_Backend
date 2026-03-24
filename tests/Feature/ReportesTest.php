<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Generar roles
        $this->artisan('db:seed', ['--class' => 'AdminUserSeeder']);
    }

    private function getOrganizadorUser()
    {
        return User::where('email', 'organizador@smartschool.com')->first();
    }

    public function test_organizador_can_list_reportes()
    {
        $user = $this->getOrganizadorUser();

        $response = $this->actingAsJwt($user)->getJson('/api/v1/reportes');

        $response->assertStatus(200);
    }

    public function test_organizador_can_create_reporte()
    {
        $user = $this->getOrganizadorUser();

        $payload = [
            'tipo' => 'Financiero',
            'descripcion' => 'Reporte del trimestre',
            'fecha' => now()->toDateString(),
            'estado' => 'activo',
        ];

        $response = $this->actingAsJwt($user)->postJson('/api/v1/reportes', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.tipo', 'Financiero');

        $this->assertDatabaseHas('_reportes_', [
            'tipo' => 'Financiero'
        ]);
    }

    public function test_organizador_can_show_reporte()
    {
        $user = $this->getOrganizadorUser();

        $createResponse = $this->actingAsJwt($user)->postJson('/api/v1/reportes', [
            'tipo' => 'Operativo',
            'descripcion' => 'Operaciones',
            'fecha' => now()->toDateString(),
            'estado' => 'activo',
        ]);
        
        $reporteId = $createResponse->json('data.id');

        $response = $this->actingAsJwt($user)->getJson("/api/v1/reportes/{$reporteId}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.tipo', 'Operativo');
    }

    public function test_organizador_can_update_reporte()
    {
        $user = $this->getOrganizadorUser();

        $createResponse = $this->actingAsJwt($user)->postJson('/api/v1/reportes', [
            'tipo' => 'Asistencia',
            'descripcion' => 'Baja asistencia',
            'fecha' => now()->toDateString(),
            'estado' => 'pendiente',
        ]);
        
        $reporteId = $createResponse->json('data.id');

        $response = $this->actingAsJwt($user)->putJson("/api/v1/reportes/{$reporteId}", [
            'tipo' => 'Asistencia Update',
            'descripcion' => 'Alta asistencia',
            'fecha' => now()->toDateString(),
            'estado' => 'finalizado',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.tipo', 'Asistencia Update');
    }

    public function test_organizador_can_delete_reporte()
    {
        $user = $this->getOrganizadorUser();

        $createResponse = $this->actingAsJwt($user)->postJson('/api/v1/reportes', [
            'tipo' => 'Borrar',
            'descripcion' => 'A borrar',
            'fecha' => now()->toDateString(),
            'estado' => 'cancelado',
        ]);
        
        $reporteId = $createResponse->json('data.id');

        $response = $this->actingAsJwt($user)->deleteJson("/api/v1/reportes/{$reporteId}");

        $response->assertStatus(204);
    }
}
