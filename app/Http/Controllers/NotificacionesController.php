<?php

namespace App\Http\Controllers;
use App\Http\Requests\NotificacionesRequest;
use App\Http\Resources\NotificacionesResource;
use App\Services\NotificacionesService;


use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    protected $notificacionesService;

    public function __construct(NotificacionesService $notificacionesService)
    {
        $this->notificacionesService = $notificacionesService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        return NotificacionesResource::collection($this->notificacionesService->NotificacionesgetAll($perPage));
    }

    public function show($id)
    {
        return new NotificacionesResource($this->notificacionesService->NotificacionesgetById($id));
    }

    public function store(NotificacionesRequest $request)
    {
        return new NotificacionesResource($this->notificacionesService->Notificacionescreate($request->validated()));
    }

    public function update(NotificacionesRequest $request, $id)
    {
        return new NotificacionesResource($this->notificacionesService->Notificacionesupdate($id, $request->validated()));
    }

    public function destroy($id)
    {
        return response()->json(['success' => $this->notificacionesService->Notificacionesdelete($id)]);
    }
}
