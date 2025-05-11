<?php

namespace App\Http\Controllers;

use App\Models\Precipitacion;
use App\Models\Observador;
use App\Models\Ubicacion;
use Carbon\Carbon;
use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

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
            /*
            if (now()->hour > 9 && now()->minute > 0 && now()->second > 0){
                return response()->json([
                    'message' => 'Solo se puede ingresar hasta las 9:00:00 am'
                ], 404);
            }
            */
            
            $validated = $request->validate([
                'ubicacion_id' => 'required|exists:ubicacion,id',
                'tipo_frecuencia_id' => 'required|numeric',
                'intervalo' => 'required|in:INI,FIN',
                'valor' => 'required|numeric',
                'fecha_registro_precipitacion' => 'required|date',
                'observador_id' => 'required|numeric',
            ]);

            /*
            $date_today = Carbon::parse($request->get('fecha_registro_precipitacion'));

            if ($date_today->toDateString() != now()->toDateString())
            {
                return response()->json([
                    'message' => 'Solo se puede registrar la fecha del dia de hoy.',
                    'campo' => "fecha_registro_precipitacion",
                ], 404);
            }
            */

            $existToday = Precipitacion::query()
                ->where('fecha_registro_precipitacion', $validated['fecha_registro_precipitacion'])
                ->where('ubicacion_id', $validated['ubicacion_id'])->count();

            if ($existToday > 0){
                return response()->json([
                    'message' => 'Ya se registro una precipitacion el dia '.$request->get('fecha_registro_precipitacion').' en esa ubicacion.',
                    'errors' => [],
                ], 404);
            }

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

    public function getAusentUbicationsPerDate(String $date = "")
    {
        try {
            $validator = Validator::make(
                ['fecha' => $date],
                ['fecha' => 'required|date_format:Y-m-d']
            );

            if (!$validator->passes()) {
                return response()->json([
                    'message' => 'Error fecha no valida.',
                    'errors' => "El formato debe ser yyyy-mm-dd",
                ], 404);
            }
            $initDate = Carbon::parse($date)->startOfDay(); // 00:00:00
            $endDate = Carbon::parse($date)->setTime(9, 0, 0);

            $ubications = ApiHelper::getAlloweds(Ubicacion::class, all:true);
            $precipitations = Precipitacion::whereBetween('fecha_registro_precipitacion', [$initDate, $endDate])->where("estado", "A")->get();
            
            $ubicationsFiltered = $ubications->filter(function ($ubication) use ($precipitations) {
                return !$precipitations->contains(function ($precipitation) use ($ubication) {
                    return $precipitation->ubicacion_id == $ubication->id;
                });
            });
            
            return response()->json([
                "fecha"=> $date,
                "ubicaciones" => $ubicationsFiltered
            ]);
        }
        catch (ValidationException $e){
            return response()->json([
                'message' => 'Error',
                'errors' => $e->errors(),
            ], 404);
        }
        
    }
}