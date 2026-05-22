<?php

namespace App\Repository\Interfaces;

interface NotaInterfaces
{
    public function NotagetAll($perPage = null);

    public function NotagetById($id);

    public function Notacreate($data);

    public function Notaupdate($id, $data);

    public function Notadelete($id);

    public function NotabyEstudiante($estudianteId, $periodoId = null);
}
