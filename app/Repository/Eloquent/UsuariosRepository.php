<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Interfaces\UsuariosInterfaces;
use Illuminate\Support\Facades\Hash;

class UsuariosRepository implements UsuariosInterfaces
{
    public function getAll($perPage = null)
    {
        // FIX: cargar relación de roles para que el Resource los incluya
        $query = User::with('roles');
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getById($id)
    {
        return User::with('roles')->find($id);
    }

    // FIX: hashear contraseña al crear usuario desde el panel admin
    public function create($data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return User::create($data);
    }

    // FIX: hashear contraseña solo si viene en la actualización
    public function update($id, $data)
    {
        $model = User::find($id);
        if (! $model) return null;

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // No actualizar contraseña si no se envió
            unset($data['password']);
        }

        $model->update($data);
        return $model->fresh('roles');
    }

    public function delete($id)
    {
        $model = User::find($id);
        if (! $model) return false;
        $model->delete();
        return true;
    }
}
