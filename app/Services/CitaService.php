<?php

namespace App\Services;

use App\Models\Cita;
use App\Repository\Interfaces\CitaInterfaces;

class CitaService
{
    protected $citaRepository;

    public function __construct(CitaInterfaces $citaRepository)
    {
        $this->citaRepository = $citaRepository;
    }

    public function getAllByUser($userId)
    {
        return $this->citaRepository->CitagetAllByUser($userId);
    }

    public function getById($id)
    {
        return $this->citaRepository->CitagetById($id);
    }

    public function create($data)
    {
        return $this->citaRepository->Citacreate($data);
    }

    public function update($id, $data)
    {
        return $this->citaRepository->Citaupdate($id, $data);
    }

    public function delete($id)
    {
        return $this->citaRepository->Citadelete($id);
    }

    public function pendientesByDestinatario($userId)
    {
        return $this->citaRepository->CitapendientesByDestinatario($userId);
    }

    public function aprobar($id, $userId)
    {
        $cita = $this->citaRepository->CitagetById($id);
        if (! $cita || $cita->destinatario_id !== $userId) {
            return null;
        }
        if ($cita->estado !== 'pendiente') {
            return false;
        }
        $this->citaRepository->Citaupdate($id, ['estado' => 'aprobada']);
        return $this->citaRepository->CitagetById($id);
    }

    public function rechazar($id, $userId)
    {
        $cita = $this->citaRepository->CitagetById($id);
        if (! $cita || $cita->destinatario_id !== $userId) {
            return null;
        }
        if ($cita->estado !== 'pendiente') {
            return false;
        }
        $this->citaRepository->Citaupdate($id, ['estado' => 'rechazada']);
        return $this->citaRepository->CitagetById($id);
    }
}
