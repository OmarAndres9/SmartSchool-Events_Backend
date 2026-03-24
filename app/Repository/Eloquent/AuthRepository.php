<?php

namespace App\Repository\Eloquent;

use App\Repository\Interfaces\AuthInterfaces;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository implements AuthInterfaces
{
    public function AttemptLogin(array $credentials)
    {
        return auth('api')->attempt($credentials);
    }

    public function GetUser()
    {
        return auth('api')->user();
    }
}
