<?php

namespace App\Repository\Interfaces;

interface PeriodoInterfaces
{
    public function PeriodogetAll($perPage = null);

    public function PeriodogetById($id);

    public function Periodocreate($data);

    public function Periodoupdate($id, $data);

    public function Periododelete($id);

    public function Periodoactivo();
}
