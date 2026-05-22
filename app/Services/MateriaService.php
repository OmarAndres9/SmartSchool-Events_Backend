<?php

namespace App\Services;

use App\Repository\Interfaces\MateriaInterfaces;

class MateriaService
{
    protected $materiaRepository;

    public function __construct(MateriaInterfaces $materiaRepository)
    {
        $this->materiaRepository = $materiaRepository;
    }

    public function getAll($perPage = null)
    {
        return $this->materiaRepository->MateriagetAll($perPage);
    }

    public function getById($id)
    {
        return $this->materiaRepository->MateriagetById($id);
    }

    public function create($data)
    {
        return $this->materiaRepository->Materiacreate($data);
    }

    public function update($id, $data)
    {
        return $this->materiaRepository->Materiaupdate($id, $data);
    }

    public function delete($id)
    {
        return $this->materiaRepository->Materiadelete($id);
    }

    public function getByDocente($docenteId)
    {
        return $this->materiaRepository->MateriagetByDocente($docenteId);
    }
}
