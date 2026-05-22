<?php

namespace App\Services;

use App\Repository\Interfaces\NotaInterfaces;

class NotaService
{
    protected $notaRepository;

    public function __construct(NotaInterfaces $notaRepository)
    {
        $this->notaRepository = $notaRepository;
    }

    public function getAll($perPage = null)
    {
        return $this->notaRepository->NotagetAll($perPage);
    }

    public function getById($id)
    {
        return $this->notaRepository->NotagetById($id);
    }

    public function create($data)
    {
        return $this->notaRepository->Notacreate($data);
    }

    public function update($id, $data)
    {
        return $this->notaRepository->Notaupdate($id, $data);
    }

    public function delete($id)
    {
        return $this->notaRepository->Notadelete($id);
    }

    public function byEstudiante($estudianteId, $periodoId = null)
    {
        return $this->notaRepository->NotabyEstudiante($estudianteId, $periodoId);
    }
}
