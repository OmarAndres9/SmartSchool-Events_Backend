<?php
namespace App\Repository\Interfaces;

interface NotificacionesInterfaces
{
    public function NotificacionesgetAll($perPage = null);
    public function NotificacionesgetById($id);
    public function Notificacionescreate($data);
    public function Notificacionesupdate($id, $data);
    public function Notificacionesdelete($id);
}