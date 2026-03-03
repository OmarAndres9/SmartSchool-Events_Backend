<?php

namespace App\Services;

use App\Repository\Interfaces\RecursosInterfaces;

class RecursosService
{
    protected $recursosRepository;

    public function __construct(RecursosInterfaces $recursosRepository)
    {
        $this->recursosRepository = $recursosRepository;
    }

    public function RecursosgetAll($perPage = null)
    {
        return $this->recursosRepository->RecursosgetAll($perPage);
    }

    public function RecursosgetById($id)
    {
        return $this->recursosRepository->RecursosgetById($id);
    }

    public function Recursoscreate($data)
    {
        return $this->recursosRepository->Recursoscreate($data);
    }

    public function Recursosupdate($id, $data)
    {
        return $this->recursosRepository->Recursosupdate($id, $data);
    }

    public function Recursosdelete($id)
    {
        return $this->recursosRepository->Recursosdelete($id);
    }
}
 