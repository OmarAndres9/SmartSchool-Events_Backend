<?php

namespace App\Repository\Eloquent;

use App\Repository\Interfaces\AuthInterfaces;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthRepository implements AuthInterfaces
{

    public function AttemptLogin(array $credentials)
    {
        try {
            return JWTAuth::attempt($credentials);
        } catch (JWTException $e) {
            return false;
        }
    }

    public function GetUser()
    {
        try {
            return JWTAuth::user();
        } catch (JWTException $e) {
            return null;
        }
    }
}