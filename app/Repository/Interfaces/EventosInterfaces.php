<?php

namespace App\Repository\Interfaces;

interface EventosInterfaces
{
    public function EventosgetAll($perPage = null);

    public function EventosgetById($id);

    public function Eventoscreate($data);

    public function Eventosupdate($id, $data);

    public function Eventosdelete($id);
}
