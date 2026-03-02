<?php

namespace App\Repository\Interfaces;

interface RecursosInterfaces
{
    public function RecursosgetAll($perPage = null);
    public function RecursosgetById($id);
    public function Recursoscreate($data);
    public function Recursosupdate($id, $data);
    public function Recursosdelete($id);
}