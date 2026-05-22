<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    public function perfil()
    {
        $user = JWTAuth::user()->load('roles');

        return response()->json([
            'user' => [
                'id'                             => $user->id,
                'name'                           => $user->name,
                'email'                          => $user->email,
                'tipo_documento'                 => $user->tipo_documento,
                'documento'                      => $user->documento,
                'roles'                          => $user->roles->pluck('name'),
                'recordatorio_email'             => $user->recordatorio_email,
                'recordatorio_anticipacion_minutos' => $user->recordatorio_anticipacion_minutos,
            ],
        ]);
    }

    public function actualizarPerfil(ProfileRequest $request)
    {
        $user = JWTAuth::user();
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Perfil actualizado',
            'user'    => $user->fresh()->load('roles'),
        ]);
    }
}
