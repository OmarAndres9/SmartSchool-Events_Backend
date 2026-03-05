<?php

namespace App\Repository\Interfaces;

interface AuthInterfaces
{
    public function AttemptLogin(array $credentials);
    public function GetUser();
}