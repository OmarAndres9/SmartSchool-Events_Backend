<?php

namespace App\Repository\Eloquent;

use App\Repository\Interfaces\RolesInterfaces;
use App\Models\Roles;

class RolesRepository implements RolesInterfaces
{
    public function RolesgetAll()
    {
        return Roles::all();
    }
    public function RolesgetById($id)
    {
        return Roles::find($id);
    }
    public function Rolescreate($data)
    {
        return Roles::create($data);
    }
    public function Rolesupdate($id, $data)
    {
        return Roles::where('id', $id)->update($data);
    }
    public function Rolesdelete($id)
    {
        return Roles::where('id', $id)->delete();
    }
}
