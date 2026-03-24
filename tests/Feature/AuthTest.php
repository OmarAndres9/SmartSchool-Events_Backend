<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ejecutar seeder para tener los roles disponibles
        $this->artisan('db:seed', ['--class' => 'AdminUserSeeder']);
    }

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'documento' => '12345678',
            'tipo_documento' => 'CC',
            'rol' => 'organizador',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id', 'name', 'email', 'roles']
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login()
    {
        // Require role creation if we don't have it, but the seeder handles it.
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user',
                     'token',
                 ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJsonStructure(['error']);
    }

    public function test_authenticated_user_can_get_profile()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAsJwt($user)->getJson('/api/v1/me');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user' => ['id', 'name', 'email']
                 ]);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAsJwt($user)->postJson('/api/v1/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Sesión cerrada exitosamente']);
    }
}
