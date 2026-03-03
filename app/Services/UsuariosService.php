<?php
namespace App\Services;
use App\Repository\Interfaces\UsuariosInterfaces;

class UsuariosService
{
    protected $usuariosRepository;

    public function __construct(UsuariosInterfaces $usuariosRepository)
    {
        $this->usuariosRepository = $usuariosRepository;
    }

    public function getAll($perPage = null)
    {
        return $this->usuariosRepository->getAll($perPage);
    }

    public function getById($id)
    {
        return $this->usuariosRepository->getById($id);
    }

    public function create($data)
    {
        return $this->usuariosRepository->create($data);
    }

    public function update($id, $data)
    {
        return $this->usuariosRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->usuariosRepository->delete($id);
    }
}
