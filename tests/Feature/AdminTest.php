<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Generar roles, incluyendo el usuario admin 'admin@smartschool.com'
        $this->artisan('db:seed', ['--class' => 'AdminUserSeeder']);
    }

    private function getAdminUser()
    {
        return User::where('email', 'admin@smartschool.com')->first();
    }

    private function getNormalUser()
    {
        return User::where('email', 'estudiante@smartschool.com')->first();
    }

    // --- ROLES ---
    public function test_admin_can_list_roles()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAsJwt($admin)->getJson('/api/v1/roles');

        $response->assertStatus(200);
    }

    public function test_normal_user_cannot_list_roles()
    {
        $user = $this->getNormalUser();

        $response = $this->actingAsJwt($user)->getJson('/api/v1/roles');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_role()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAsJwt($admin)->postJson('/api/v1/roles', [
            'name' => 'nuevo-rol'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('roles', ['name' => 'nuevo-rol']);
    }

    // --- PERMISOS ---
    public function test_admin_can_list_permissions()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAsJwt($admin)->getJson('/api/v1/permissions');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_permission()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAsJwt($admin)->postJson('/api/v1/permissions', [
            'name' => 'nuevo-permiso'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('permissions', ['name' => 'nuevo-permiso']);
    }

    // --- USUARIOS ---
    public function test_admin_can_list_users()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAsJwt($admin)->getJson('/api/v1/usuarios');

        $response->assertStatus(200);
    }

    public function test_admin_can_assign_roles_to_user()
    {
        $admin = $this->getAdminUser();
        $user = $this->getNormalUser();

        $response = $this->actingAsJwt($admin)->postJson("/api/v1/users/{$user->id}/roles", [
            'roles' => ['docente']
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Roles asignados correctamente']);
        
        $this->assertTrue($user->fresh()->hasRole('docente'));
    }
}
