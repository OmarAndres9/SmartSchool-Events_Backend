<?php

namespace App\Services;

use App\Repository\Interfaces\EventosInterfaces;

class EventosService
{
    protected $eventosRepository;

    public function __construct(EventosInterfaces $eventosRepository)
    {
        $this->eventosRepository = $eventosRepository;
    }

    public function getAll($perPage = null)
    {
        return $this->eventosRepository->EventosgetAll($perPage);
    }

    public function getById($id)
    {
        return $this->eventosRepository->EventosgetById($id);
    }

    public function create($data)
    {
        return $this->eventosRepository->Eventoscreate($data);
    }

    public function update($id, $data)
    {
        return $this->eventosRepository->Eventosupdate($id, $data);
    }

    public function delete($id)
    {
        return $this->eventosRepository->Eventosdelete($id);
    }
}