<?php

namespace App\Http\Controllers;

use App\Models\Precipitacion;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
class PrecipitacionController extends Controller
{
    public function getAll()
    {
        return response()->json(ApiHelper::getAlloweds(Precipitacion::class), 200);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ubicacion_id' => 'required|exists:ubicacion,id',
            'tipo_frecuencia_id' => 'required|numeric',
            'intervalo' => 'required|in:INI,FIN',
            'valor' => 'required|numeric',
            'observador_id' => 'required|numeric',
        ]);
        $precipitacion = Precipitacion::create($validated);
        return response()->json([
            'message' => 'Precipitacion creada exitosamente',
            'data' => $precipitacion,
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ubicacion_id' => 'required|exists:ubicacion,id',
            'tipo_frecuencia_id' => 'required',
            'intervalo' => 'required|string|max:3',
            'valor' => 'required|numeric',
            'observador_id' => 'required|exists:observador,id',
        ]);
        $precipitacion = Precipitacion::findOrFail($id);
        $precipitacion->fill($validated);
        $precipitacion->save();
        return response()->json([
            'message' => 'Precipitacion actualizada exitosamente',
            'data' => $precipitacion,
        ], 200);
    }

    public function destroy($id)
    {
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
    }
}