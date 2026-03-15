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
        // ── Limpiar caché de permisos de Spatie antes de crear roles ──────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Crear roles con guard_name explícito (requerido por Spatie) ────────
        // firstOrCreate necesita TODOS los campos del índice único: name + guard_name
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

        // ── Crear usuario administrador ────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'           => 'Administrador',
                'password'       => Hash::make('admin1234'),
                'tipo_documento' => 'CC',
                'documento'      => '1234567890',
            ]
        );

        // Asignar rol admin (guard_name debe coincidir)
        $admin->assignRole('admin');

        // ── Crear usuario organizador de prueba ───────────────────────────────
        $organizador = User::firstOrCreate(
            ['email' => 'organizador@smartschool.com'],
            [
                'name'           => 'Organizador Demo',
                'password'       => Hash::make('organiz1234'),
                'tipo_documento' => 'CC',
                'documento'      => '9876543210',
            ]
        );

        $organizador->assignRole('organizador');

        $this->command->info('✅ Roles creados: admin, organizador, docente, estudiante, acudiente');
        $this->command->info('✅ Usuario admin: admin@admin.com / admin1234');
        $this->command->info('✅ Usuario organizador: organizador@smartschool.com / organiz1234');
    }
}
