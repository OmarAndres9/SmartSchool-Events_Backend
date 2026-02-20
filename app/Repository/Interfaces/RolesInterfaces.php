<?php

namespace App\Repository\Interfaces;

interface RolesInterfaces
{
    public function RolesgetAll();
    public function RolesgetById($id);
    public function Rolescreate($data);
    public function Rolesupdate($id, $data);
    public function Rolesdelete($id);
}

