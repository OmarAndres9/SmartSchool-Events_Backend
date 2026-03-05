<?php

namespace App\Http\Controllers;

use App\Services\AutService;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AutService $authService)
    {
        $this->authService = $authService;
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
                'token' => $token
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
                'message' => 'Logout successful'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not logout'], 500);
        }
    }
}
