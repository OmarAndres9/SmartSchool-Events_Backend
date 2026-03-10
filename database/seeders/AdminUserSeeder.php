<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define el rol de administrador
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        // Crea el usuario admin
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'Administrador@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => \Illuminate\Support\Facades\Hash::make('admin1234'), // Contraseña: admin1234
                'tipo_documento' => 'CC',
                'documento' => '1234567890',
            ]
        );

        // Asignar el rol al usuario
        $admin->assignRole($role);
    }
}
