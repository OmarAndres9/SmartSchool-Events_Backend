<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AutService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users',
            'password'       => 'required|string|min:6|confirmed',
            'documento'      => 'required|string|max:12|unique:users',
            'tipo_documento' => 'required|string|max:10',
            'rol'            => 'required|string',
        ]);

        $user = User::create([
            'name'           => $validatedData['name'],
            'email'          => $validatedData['email'],
            'password'       => Hash::make($validatedData['password']),
            'documento'      => $validatedData['documento'],
            'tipo_documento' => $validatedData['tipo_documento'],
        ]);

        // FIX: buscar el rol con guard_name='api' para que coincida con el seeder
        $roleName = strtolower($validatedData['rol']);
        $role = Role::where('name', $roleName)->where('guard_name', 'api')->first();

        if ($role) {
            $user->assignRole($role);
        }

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user'    => $user->load('roles'),
        ], 201);
    }

    public function login(AuthRequest $request)
    {
        $credentials = $request->validated();

        try {
            $result = $this->authService->login($credentials);

            return response()->json([
                'message' => 'Login exitoso',
                // FIX: cargar roles para que el frontend pueda leer user.roles[0].name
                'user'    => $result['user']->load('roles'),
                'token'   => $result['token'],
            ], 200);

        } catch (\Exception $e) {
            $isInvalidCredentials = $e->getMessage() === 'Invalid credentials';

            return response()->json([
                'error' => $isInvalidCredentials
                    ? 'Credenciales incorrectas'
                    : 'Error al generar el token',
            ], $isInvalidCredentials ? 401 : 500);
        }
    }

    public function me()
    {
        $user = $this->authService->GetUser();

        if (! $user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // FIX: incluir roles en la respuesta del perfil
        return response()->json([
            'user' => $user->load('roles'),
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logout();
            return response()->json(['message' => 'Sesión cerrada exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cerrar sesión'], 500);
        }
    }
}
