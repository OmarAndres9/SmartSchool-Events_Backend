<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'AdminUserSeeder']);

        // Extraer usuarios generados
        $this->user = User::where('email', 'estudiante@smartschool.com')->first();
        $this->admin = User::where('email', 'admin@smartschool.com')->first();
    }

    /** @test */
    public function test_public_endpoints()
    {
        // LOGIN
        $this->postJson('/api/v1/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ])->assertStatus(401); // Credenciales invalidas

        // REGISTER
        $this->postJson('/api/v1/register', [
            'name' => 'Juan',
            'email' => 'juan@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'tipo_documento' => 'CC',
            'documento' => '22334455',
            'rol' => 'estudiante'
        ])->assertStatus(201);

        // RESET PASSWORD EMAIL
        $this->postJson('/api/v1/password/email', [
            'email' => 'juan@test.com'
        ])->assertStatus(200);

        // RESET PASSWORD
        $this->postJson('/api/v1/password/reset', [
            'token' => 'fake-token',
            'email' => 'juan@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertStatus(400);
    }

    /** @test */
    public function test_authenticated_user_endpoints()
    {
        $this->actingAsJwt($this->user);

        // ME
        $this->getJson('/api/v1/me')->assertStatus(200);

        // LOGOUT
        $this->postJson('/api/v1/logout')->assertStatus(200);

        // NOTIFICACIONES (resource)
        $this->getJson('/api/v1/notificaciones')->assertStatus(200);
        $this->postJson('/api/v1/notificaciones', [])->assertStatus(422);
    }

    /** @test */
    public function test_admin_endpoints()
    {
        $this->actingAsJwt($this->admin);

        // ROLES
        $this->getJson('/api/v1/roles')->assertStatus(200);
        $this->postJson('/api/v1/roles', [])->assertStatus(422);

        // PERMISSIONS
        $this->getJson('/api/v1/permissions')->assertStatus(200);

        // USUARIOS
        $this->getJson('/api/v1/usuarios')->assertStatus(200);

        // ASIGNAR ROLES
        $this->postJson("/api/v1/users/{$this->user->id}/roles", [
            'roles' => ['admin']
        ])->assertStatus(200);
    }

    /** @test */
    public function test_admin_and_organizador_endpoints()
    {
        $this->actingAsJwt($this->admin);

        // EVENTOS
        $this->getJson('/api/v1/eventos')->assertStatus(200);

        // MIS EVENTOS
        $this->getJson('/api/v1/eventos/mis-eventos')->assertStatus(200);

        // RECURSOS
        $this->getJson('/api/v1/recursos')->assertStatus(200);

        // REPORTES
        $this->getJson('/api/v1/reportes')->assertStatus(200);

        // ASIGNAR RECURSO
        $this->postJson('/api/v1/eventos/1/recursos', [
            'recurso_id' => 1
        ])->assertStatus(404); // porque no existe aún

        // DESASIGNAR RECURSO
        $this->deleteJson('/api/v1/eventos/1/recursos/1')
            ->assertStatus(404);
    }
}