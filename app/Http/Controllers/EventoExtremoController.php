<?php

namespace App\Http\Controllers;

use App\Models\EventoExtremo;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Validation\ValidationException;
class EventoExtremoController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        $extreme_events_filtered = ApiHelper::getAlloweds(EventoExtremo::class, $perPage)->pluck('id');
        $extreme_events = EventoExtremo::whereIn('id', $extreme_events_filtered)
            ->with(['tipo_intensidad_evento', 'tipo_evento', 'observador', 'ubicacion'])
            ->get();
        return response()->json($extreme_events, 200);
    }
    public function getOne($id)
    {
        $ubicacion = EventoExtremo::findOrFail($id);
        if ($ubicacion->estado === 'B') {
            return response()->json([
                'message' => 'Evento extremo no disponible'
            ], 404);
        }
        return response()->json($ubicacion, 200);
    }
    public function store(Request $request)
    {
        try{
            $request->merge([
                'ubicacion_id' => $request->get('ubicacion_id'),
                'tipo_evento_id' => $request->get('tipo_evento_id'),
                'tipo_intensidad_evento_id' => $request->get('tipo_intensidad_evento_id'),
                'numero_veces' => $request->get('numero_veces'),
                'observacion' => $request->get('observacion'),
                'observador_id' => $request->get('observador_id'),
                'fecha_registro_evento' => $request->get('fecha_registro_evento', now()),
                'estado_cultivo_papa' => $request->get('estado_cultivo_papa', 0),
                'estado_cultivo_haba' => $request->get('estado_cultivo_haba', 0),
                'estado_cultivo_tomate' => $request->get('estado_cultivo_tomate', 0),
                'estado_cultivo_cebolla' => $request->get('estado_cultivo_cebolla', 0),
                'estado_cultivo_maiz' => $request->get('estado_cultivo_maiz', 0),
                'estado_cultivo_zanahoria' => $request->get('estado_cultivo_zanahoria', 0),
                'estado_cultivo_durazno' => $request->get('estado_cultivo_durazno', 0),
                'estado_cultivo_manzana' => $request->get('estado_cultivo_manzana', 0),
            ]);
            $validated = $request->validate([
                'ubicacion_id' => 'required|exists:ubicacion,id',
                'tipo_evento_id' => 'required|exists:tipo_evento,id',
                'tipo_intensidad_evento_id' => 'required|exists:tipo_intensidad_evento,id',
                'numero_veces' => 'required|numeric',
                'observacion' => 'required|string',
                'observador_id' => 'required|exists:observador,id',
                'fecha_registro_evento' => 'date',
                'estado_cultivo_papa' => 'numeric',
                'estado_cultivo_haba' => 'numeric',
                'estado_cultivo_tomate' => 'numeric',
                'estado_cultivo_cebolla' => 'numeric',
                'estado_cultivo_maiz' => 'numeric',
                'estado_cultivo_zanahoria' => 'numeric',
                'estado_cultivo_durazno' => 'numeric',
                'estado_cultivo_manzana' => 'numeric',
                'otro_cultivo' => 'string'
            ]);
            $precipitacion = EventoExtremo::create($validated);
            return response()->json([
                'message' => 'Evento extremo creado exitosamente',
                'data' => $precipitacion,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al crear evento extremo.',
                'errors' => $e->errors(),
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        try{
            
            $validated = $request->validate([
                'ubicacion_id' => 'required|exists:ubicacion,id',
                'tipo_evento_id' => 'required|exists:tipo_evento,id',
                'tipo_intensidad_evento_id' => 'required|exists:tipo_intensidad_evento,id',
                'numero_veces' => 'required|numeric',
                'observacion' => 'required|string',
                'observador_id' => 'required|exists:observador,id',
                'fecha_registro_evento' => 'date',
                'estado_cultivo_papa' => 'numeric',
                'estado_cultivo_haba' => 'numeric',
                'estado_cultivo_tomate' => 'numeric',
                'estado_cultivo_cebolla' => 'numeric',
                'estado_cultivo_maiz' => 'numeric',
                'estado_cultivo_zanahoria' => 'numeric',
                'estado_cultivo_durazno' => 'numeric',
                'estado_cultivo_manzana' => 'numeric',
                'otro_cultivo' => 'nullable|string'
            ]);
            $extreme_evento = EventoExtremo::findOrFail($id);
            $extreme_evento->fill($validated);
            $extreme_evento->save();
            return response()->json([
                'message' => 'Evento extremo actualizado exitosamente',
                'data' => $extreme_evento,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al actualizar evento extremo.',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    public function destroy($id)
    {
        try{
            $precipitacion = EventoExtremo::findOrFail($id);

            if ($precipitacion->estado === 'B') {
                return response()->json([
                    'message' => 'El evento extremo ya está inactivo.'
                ], 400);
            }

            $precipitacion->estado = 'B';
            $precipitacion->fecha_eliminacion = now();
            
            $precipitacion->save();

            return response()->json([
                'message' => 'Evento extremo eliminado exitosamente.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al eliminar evento extremo.',
                'errors' => $e->errors(),
            ], 404);
        }
    }
}