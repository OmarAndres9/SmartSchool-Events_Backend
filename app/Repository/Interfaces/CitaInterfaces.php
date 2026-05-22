<?php

namespace App\Repository\Interfaces;

interface CitaInterfaces
{
    public function CitagetAllByUser($userId);

    public function CitagetById($id);

    public function Citacreate($data);

    public function Citaupdate($id, $data);

    public function Citadelete($id);

    public function CitapendientesByDestinatario($userId);
}
