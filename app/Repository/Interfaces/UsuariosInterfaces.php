<?php
namespace App\Repository\Interfaces;

interface UsuariosInterfaces
{
    public function getAll($perPage = null);
    public function getById($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}