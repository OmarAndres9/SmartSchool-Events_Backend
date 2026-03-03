<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RecursosRequest;
use App\Services\RecursosService;
use App\Http\Resources\RecursosResource;

class RecursosController extends Controller
{
    protected $recursosService;

    public function __construct(RecursosService $recursosService)
    {
        $this->recursosService = $recursosService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? null;
        $recursos = $this->recursosService->RecursosgetAll($perPage);
        return RecursosResource::collection($recursos);
    }

    public function store(RecursosRequest $request)
    {
        $data = $request->validated();
        $recurso = $this->recursosService->Recursoscreate($data);
        return (new RecursosResource($recurso))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        $recurso = $this->recursosService->RecursosgetById($id);
        if (!$recurso) {
            return response()->json(['message' => 'Recurso no encontrado'], 404);
        }
        return new RecursosResource($recurso);
    }

    public function update(RecursosRequest $request, $id)
    {
        $data = $request->validated();
        $recurso = $this->recursosService->Recursosupdate($id, $data);
        if (!$recurso) {
            return response()->json(['message' => 'Recurso no encontrado'], 404);
        }
        return new RecursosResource($recurso);
    }

    public function destroy($id)
    {
        $deleted = $this->recursosService->Recursosdelete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Recurso no encontrado'], 404);
        }
        return response()->json(null, 204);
    }
}
