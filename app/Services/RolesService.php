<?php

namespace App\Services;

use App\Repository\Interfaces\RolesInterfaces;

class RolesService
{
    protected $rolesRepository;

    public function __construct(RolesInterfaces $rolesRepository)
    {
        $this->rolesRepository = $rolesRepository;
    }

    public function RolesgetAll()
    {
        return $this->rolesRepository->RolesgetAll();
    }

    public function RolesgetById($id)
    {
        return $this->rolesRepository->RolesgetById($id);
    }

    public function Rolescreate($data)
    {
        return $this->rolesRepository->Rolescreate($data);
    }

    public function Rolesupdate($id, $data)
    {
        return $this->rolesRepository->Rolesupdate($id, $data);
    }

    public function Rolesdelete($id)
    {
        return $this->rolesRepository->Rolesdelete($id);
    }
}
