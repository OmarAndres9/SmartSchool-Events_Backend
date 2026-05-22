<?php

namespace App\Repository\Interfaces;

interface MateriaInterfaces
{
    public function MateriagetAll($perPage = null);

    public function MateriagetById($id);

    public function Materiacreate($data);

    public function Materiaupdate($id, $data);

    public function Materiadelete($id);
}
