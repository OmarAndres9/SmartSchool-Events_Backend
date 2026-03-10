<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AutService;
use Illuminate\Http\Request;

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'documento' => 'required|string|max:12|unique:users',
            'tipo_documento' => 'required|string|max:255',
            'rol' => 'required|string',
        ]);

        $user = \App\Models\User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validatedData['password']),
            'documento' => $validatedData['documento'],
            'tipo_documento' => $validatedData['tipo_documento'],
        ]);

        // Clean user role input to match database roles if needed (lowercase)
        $roleName = strtolower($validatedData['rol']);
        if (\Spatie\Permission\Models\Role::where('name', $roleName)->exists()) {
            $user->assignRole($roleName);
        }

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user
        ], 201);
    }

    public function login(AuthRequest $request)
    {
        $credentials = $request->validated();

        try {
            $result = $this->authService->login($credentials);

            $user = $result['user'];
            $token = $result['token'];

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            $invalidCredentials = 'Invalid credentials';
            $statusCode = $e->getMessage() === $invalidCredentials ? 401 : 500;
            $errorMessage = $e->getMessage() === $invalidCredentials
                ? $invalidCredentials
                : 'Could not create token';

            return response()->json(['error' => $errorMessage], $statusCode);
        }
    }

    public function me()
    {
        $user = $this->authService->GetUser();

        return response()->json(compact('user'));
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logout();

            return response()->json([
                'message' => 'Logout successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not logout'], 500);
        }
    }
}
