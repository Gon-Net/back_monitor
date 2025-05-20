<?php

namespace App\Http\Controllers;
use App\Models\Visita;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Validation\ValidationException;

class VisitaController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(Visita::class, $perPage), 200);
    }
    public function getOne(Request $request, $id)
    {
        $ubicacion = Visita::findOrFail($id);
        if ($ubicacion->estado === 'B') {
            return response()->json([
                'message' => 'Visita no disponible'
            ], 404);
        }
        return response()->json($ubicacion, 200);
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ubicacion_id' => 'required|exists:ubicacion,id',
                'usuario' => 'required|numeric',
                'observacion' => 'required|string'
            ]);
            $visit = Visita::create($validated);
            return response()->json([
                'message' => 'Visita creada exitosamente',
                'data' => $visit,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al crear.',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $validated = $request->validate([
                'ubicacion_id' => 'required|exists:ubicacion,id',
                'usuario' => 'required|numeric',
                'observacion' => 'required|string'
            ]);
            $visit = Visita::findOrFail($id);
            if ($visit->estado == "B"){
                return response()->json([
                'message' => 'Visita no valida.',
            ], 404);
            }
            $visit->fill($validated);
            $visit->save();
            return response()->json([
                'message' => 'Visita actualizado exitosamente',
                'data' => $visit,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al actualizar visita.',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    public function destroy($id)
    {
        try{
            $visit = Visita::findOrFail($id);
            if ($visit->estado === 'B') {
                return response()->json([
                    'message' => 'El evento extremo ya está inactivo.'
                ], 400);
            }

            $visit->estado = 'B';
            $visit->save();
            return response()->json([
                'message' => 'Visita eliminado exitosamente.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al eliminar visita.',
                'errors' => $e->errors(),
            ], 404);
        }
    }
}
