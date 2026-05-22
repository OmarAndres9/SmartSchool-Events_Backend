<?php

namespace App\Http\Controllers;

use App\Http\Requests\CitaRequest;
use App\Services\CitaService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CitaController extends Controller
{
    protected $citaService;

    public function __construct(CitaService $citaService)
    {
        $this->citaService = $citaService;
    }

    public function index()
    {
        $user  = JWTAuth::user();
        $citas = $this->citaService->getAllByUser($user->id);

        return response()->json(['citas' => $citas]);
    }

    public function store(CitaRequest $request)
    {
        $user = JWTAuth::user();
        $data = $request->validated();
        $data['solicitante_id'] = $user->id;

        $cita = $this->citaService->create($data);
        $cita->load(['solicitante:id,name', 'destinatario:id,name,email']);

        return response()->json([
            'message' => 'Solicitud de cita creada correctamente. Pendiente de aprobación.',
            'cita'    => $cita,
        ], 201);
    }

    public function aprobar($id)
    {
        $userId = JWTAuth::user()->id;
        $cita   = $this->citaService->aprobar($id, $userId);

        if ($cita === null) {
            return response()->json(['message' => 'Cita no encontrada o no tienes permiso.'], 404);
        }

        if ($cita === false) {
            return response()->json(['message' => 'La cita ya fue procesada.'], 422);
        }

        $cita->load(['solicitante:id,name', 'destinatario:id,name,email']);

        return response()->json([
            'message' => 'Cita aprobada correctamente.',
            'cita'    => $cita,
        ]);
    }

    public function rechazar($id)
    {
        $userId = JWTAuth::user()->id;
        $cita   = $this->citaService->rechazar($id, $userId);

        if ($cita === null) {
            return response()->json(['message' => 'Cita no encontrada o no tienes permiso.'], 404);
        }

        if ($cita === false) {
            return response()->json(['message' => 'La cita ya fue procesada.'], 422);
        }

        return response()->json(['message' => 'Cita rechazada.']);
    }

    public function pendientes()
    {
        $user  = JWTAuth::user();
        $citas = $this->citaService->pendientesByDestinatario($user->id);

        return response()->json(['citas' => $citas]);
    }
}
