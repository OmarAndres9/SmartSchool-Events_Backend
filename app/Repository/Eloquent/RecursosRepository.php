<?php

namespace App\Repository\Eloquent;

use App\Models\Recursos;
use App\Repository\Interfaces\RecursosInterfaces;

class RecursosRepository implements RecursosInterfaces
{
    public function RecursosgetAll($perPage = null)
    {
        return $perPage ? Recursos::with('eventos')->paginate($perPage) : Recursos::with('eventos')->get();
    }

    public function RecursosgetById($id)
    {
        return Recursos::find($id);
    }

    public function Recursoscreate($data)
    {
        return Recursos::create($data);
    }

    public function Recursosupdate($id, $data)
    {
        $model = Recursos::find($id);
        if (! $model) {
            return null;
        }
        $model->update($data);

        return $model;
    }

    public function Recursosdelete($id)
    {
        $model = Recursos::find($id);
        if (! $model) {
            return false;
        }
        $model->delete();

        return true;
    }
}
