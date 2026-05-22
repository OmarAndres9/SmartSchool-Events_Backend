<?php

namespace App\Services;

use App\Repository\Interfaces\AuthInterfaces;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthInterfaces $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login($credentials)
    {
        $token = $this->authRepository->AttemptLogin($credentials);

        if ($token) {
            $user = $this->authRepository->GetUser();

            return [
                'token' => $token,
                'user' => $user,
            ];
        }

        throw new \Exception('Credenciales incorrectas');
    }

    public function refresh()
    {
        return \Tymon\JWTAuth\Facades\JWTAuth::refresh(\Tymon\JWTAuth\Facades\JWTAuth::getToken());
    }

    public function logout()
    {
        \Tymon\JWTAuth\Facades\JWTAuth::invalidate(\Tymon\JWTAuth\Facades\JWTAuth::getToken());
    }

    public function GetUser()
    {
        return $this->authRepository->GetUser();
    }
}
