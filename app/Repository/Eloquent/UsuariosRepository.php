<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Interfaces\UsuariosInterfaces;

class UsuariosRepository implements UsuariosInterfaces
{
    public function getAll($perPage = null)
    {
        return $perPage ? User::paginate($perPage) : User::all();
    }

    public function getById($id)
    {
        return User::find($id);
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function update($id, $data)
    {
        $model = User::find($id);
        if (! $model) {
            return null;
        }
        $model->update($data);

        return $model;
    }

    public function delete($id)
    {
        $model = User::find($id);
        if (! $model) {
            return false;
        }
        $model->delete();

        return true;
    }
}
