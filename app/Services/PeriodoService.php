<?php

namespace App\Services;

use App\Repository\Interfaces\PeriodoInterfaces;

class PeriodoService
{
    protected $periodoRepository;

    public function __construct(PeriodoInterfaces $periodoRepository)
    {
        $this->periodoRepository = $periodoRepository;
    }

    public function getAll($perPage = null)
    {
        return $this->periodoRepository->PeriodogetAll($perPage);
    }

    public function getById($id)
    {
        return $this->periodoRepository->PeriodogetById($id);
    }

    public function create($data)
    {
        return $this->periodoRepository->Periodocreate($data);
    }

    public function update($id, $data)
    {
        return $this->periodoRepository->Periodoupdate($id, $data);
    }

    public function delete($id)
    {
        return $this->periodoRepository->Periododelete($id);
    }

    public function activo()
    {
        return $this->periodoRepository->Periodoactivo();
    }
}
