<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos antes de crear roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles con guard_name 'api'
        $roles = [
            ['name' => 'admin',       'guard_name' => 'api'],
            ['name' => 'organizador', 'guard_name' => 'api'],
            ['name' => 'docente',     'guard_name' => 'api'],
            ['name' => 'estudiante',  'guard_name' => 'api'],
            ['name' => 'acudiente',   'guard_name' => 'api'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                $roleData
            );
        }

        // ── Administrador principal ────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@smartschool.com'],
            [
                'name'           => 'Administrador SmartSchool',
                'password'       => Hash::make('Admin2026*'),
                'tipo_documento' => 'CC',
                'documento'      => '1000000001',
            ]
        );
        $admin->syncRoles(['admin']);

        // ── Organizador de eventos ─────────────────────────────────────────────
        $organizador = User::firstOrCreate(
            ['email' => 'organizador@smartschool.com'],
            [
                'name'           => 'Organizador de Eventos',
                'password'       => Hash::make('Organiz2026*'),
                'tipo_documento' => 'CC',
                'documento'      => '1000000002',
            ]
        );
        $organizador->syncRoles(['organizador']);

        // ── Docente de prueba ──────────────────────────────────────────────────
        $docente = User::firstOrCreate(
            ['email' => 'docente@smartschool.com'],
            [
                'name'           => 'Docente Demo',
                'password'       => Hash::make('Docente2026*'),
                'tipo_documento' => 'CC',
                'documento'      => '1000000003',
            ]
        );
        $docente->syncRoles(['docente']);

        // ── Estudiante de prueba ───────────────────────────────────────────────
        $estudiante = User::firstOrCreate(
            ['email' => 'estudiante@smartschool.com'],
            [
                'name'           => 'Estudiante Demo',
                'password'       => Hash::make('Estud2026*'),
                'tipo_documento' => 'TI',
                'documento'      => '1000000004',
            ]
        );
        $estudiante->syncRoles(['estudiante']);

        $this->command->info('');
        $this->command->info('✅ Roles creados: admin, organizador, docente, estudiante, acudiente');
        $this->command->info('');
        $this->command->info('👤 Usuarios de prueba:');
        $this->command->info('   admin@smartschool.com       / Admin2026*');
        $this->command->info('   organizador@smartschool.com / Organiz2026*');
        $this->command->info('   docente@smartschool.com     / Docente2026*');
        $this->command->info('   estudiante@smartschool.com  / Estud2026*');
        $this->command->info('');
    }
}
