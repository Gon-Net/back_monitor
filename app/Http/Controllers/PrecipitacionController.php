<?php

namespace App\Http\Controllers;

use App\Models\Precipitacion;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Validation\ValidationException;
class PrecipitacionController extends Controller
{
    public function getAll(Request $request)
    {
        $ubicacion_id = $request->input('ubicacion_id');

        $perPage = $request->input('items', 100);

        $precipitationsFiltered = ApiHelper::getAlloweds(Precipitacion::class, $perPage)->pluck('id');
        $precipitations = Precipitacion::whereIn('id', $precipitationsFiltered);
        if ($ubicacion_id !== null){
            $precipitations = $precipitations->where('ubicacion_id', $ubicacion_id);
        }

        $precipitationsComplete = $precipitations
            ->with(['ubicacion', 'observador', 'tipo_frecuencia'])
            ->paginate($perPage);
        return response()->json($precipitationsComplete, 200);
    }
    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'ubicacion_id' => 'required|exists:ubicacion,id',
                'tipo_frecuencia_id' => 'required|numeric',
                'intervalo' => 'required|in:INI,FIN',
                'valor' => 'required|numeric',
                'fecha_registro_precipitacion' => 'required|date',
                'observador_id' => 'required|numeric',
            ]);
            $precipitacion = Precipitacion::create($validated);
            return response()->json([
                'message' => 'Precipitacion creada exitosamente',
                'data' => $precipitacion,
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
                'tipo_frecuencia_id' => 'required',
                'intervalo' => 'required|string|max:3',
                'valor' => 'required|numeric',
                'fecha_registro_precipitacion' => 'required|date',
                'observador_id' => 'required|exists:observador,id',
            ]);
            $precipitacion = Precipitacion::findOrFail($id);
            $precipitacion->fill($validated);
            $precipitacion->save();
            return response()->json([
                'message' => 'Precipitacion actualizada exitosamente',
                'data' => $precipitacion,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al actualizar.',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    public function destroy($id)
    {
        try{
            $precipitacion = Precipitacion::findOrFail($id);

            if ($precipitacion->estado === 'B') {
                return response()->json([
                    'message' => 'La precipitacion ya está inactivo.'
                ], 400);
            }

            $precipitacion->estado = 'B';
            $precipitacion->fecha_eliminacion = now();
            
            $precipitacion->save();

            return response()->json([
                'message' => 'Precipitacion eliminada exitosamente.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al eliminar.',
                'errors' => $e->errors(),
            ], 404);
        }
    }
}