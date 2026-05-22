<?php

namespace App\Repository\Eloquent;

use App\Models\Cita;
use App\Repository\Interfaces\CitaInterfaces;

class CitaRepository implements CitaInterfaces
{
    public function CitagetAllByUser($userId)
    {
        return Cita::where('solicitante_id', $userId)
            ->orWhere('destinatario_id', $userId)
            ->with(['solicitante:id,name', 'destinatario:id,name,email'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function CitagetById($id)
    {
        return Cita::with(['solicitante:id,name', 'destinatario:id,name,email'])->find($id);
    }

    public function Citacreate($data)
    {
        return Cita::create($data);
    }

    public function Citaupdate($id, $data)
    {
        $affected = Cita::where('id', $id)->update($data);
        if (! $affected) return null;
        return Cita::find($id);
    }

    public function Citadelete($id)
    {
        return (bool) Cita::destroy($id);
    }

    public function CitapendientesByDestinatario($userId)
    {
        return Cita::where('destinatario_id', $userId)
            ->where('estado', 'pendiente')
            ->with(['solicitante:id,name'])
            ->orderBy('fecha_solicitada')
            ->get();
    }
}
