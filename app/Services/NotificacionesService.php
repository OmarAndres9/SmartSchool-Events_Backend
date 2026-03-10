<?php

namespace App\Services;

use App\Repository\Interfaces\NotificacionesInterfaces;

class NotificacionesService
{
    protected $notificacionesRepository;

    public function __construct(NotificacionesInterfaces $notificacionesRepository)
    {
        $this->notificacionesRepository = $notificacionesRepository;
    }

    public function NotificacionesgetAll($perPage = null)
    {
        return $this->notificacionesRepository->NotificacionesgetAll($perPage);
    }

    public function NotificacionesgetById($id)
    {
        return $this->notificacionesRepository->NotificacionesgetById($id);
    }

    public function Notificacionescreate($data)
    {
        return $this->notificacionesRepository->Notificacionescreate($data);
    }

    public function Notificacionesupdate($id, $data)
    {
        return $this->notificacionesRepository->Notificacionesupdate($id, $data);
    }

    public function Notificacionesdelete($id)
    {
        return $this->notificacionesRepository->Notificacionesdelete($id);
    }
}
