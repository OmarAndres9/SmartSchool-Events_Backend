<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Interfaces\UsuariosInterfaces;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UsuariosRepository implements UsuariosInterfaces
{
    public function getAll($perPage = null)
    {
        $page     = request()->query('page', 1);
        $limit    = $perPage ?? 15;
        $prefix   = $this->getListCachePrefix();
        $cacheKey = "{$prefix}list_p{$page}_l{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($limit) {
            return User::with('roles')->orderBy('created_at', 'desc')->paginate($limit);
        });
    }

    public function getById($id)
    {
        return User::with('roles')->find($id);
    }

    public function create($data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user = User::create($data);
        $this->flushListCache();
        return $user;
    }

    public function update($id, $data)
    {
        $model = User::find($id);
        if (! $model) return null;

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $model->update($data);
        $this->flushListCache();
        // Invalidar caché de roles de este usuario
        Cache::forget("user_roles_{$id}");
        return $model->fresh('roles');
    }

    public function delete($id)
    {
        $model = User::find($id);
        if (! $model) return false;
        $model->delete();
        $this->flushListCache();
        Cache::forget("user_roles_{$id}");
        return true;
    }

    private function getListCachePrefix(): string
    {
        return 'usuarios_list_v' . Cache::rememberForever('usuarios_list_version', fn () => 1) . '_';
    }

    private function flushListCache(): void
    {
        $version = Cache::rememberForever('usuarios_list_version', fn () => 1);
        Cache::forever('usuarios_list_version', $version + 1);
    }
}
