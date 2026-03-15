<?php

namespace App\Repository\Interfaces;

interface EventosInterfaces
{
    public function EventosgetAll($perPage = null);
    public function EventosgetById($id);
    public function Eventoscreate($data);
    public function Eventosupdate($id, $data);
    public function Eventosdelete($id);
    // FIX: métodos faltantes que el frontend necesita
    public function EventosgetByUser($userId);
    public function EventosgetByTipo($tipo);
}
