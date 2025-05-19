<?php

namespace App\Http\Controllers;
use App\Models\Forecast;
use App\Models\Prediccion;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class PrediccionController extends Controller
{
    public function getAll(Request $request)
    {
        $ubication_id = $request->input('ubication_id', null);
        $date = $request->input('fecha', null);
        if ($ubication_id == null){
            return response()->json([
                'message' => 'Se debe enviar una ubication_id'
            ], 404);
        }
        if ($date == null){
            return response()->json([
                'message' => 'Se debe enviar una fecha'
            ], 404);
        }
        $ubication = Ubicacion::findOrFail($ubication_id);
        if ($ubication->estado == 'B'){
            return response()->json([
                'message' => 'Se debe enviar una ubicacion disponible.'
            ], 404);
        }
        $values = ApiHelper::getAlloweds(Prediccion::class, all: true);
        return response()->json($values, 200);
    }
    private function save_new_forecasts($data){
        DB::beginTransaction();
        try {
            $rules = [
                'hora' => 'required|numeric',
                'dia' => 'required|string|max:20',
                'velocidad_viento' => 'nullable|numeric',
                'direccion_viento' => 'nullable|string|max:10',
                'temperatura' => 'nullable|numeric',
                'humedad' => 'required|numeric',
                'probabilidad_lluvia' => 'required|numeric',
                'detalle' => 'required|string',
                'indice_uv' => 'required|numeric',
                'descripcion' => 'required|string',
                'maximo' => 'required|numeric',
                'minimo' =>  'required|numeric'
            ];
            $count = 0;
            foreach ($data as $row) {
                $validator = Validator::make($row, $rules);
                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first());
                }
                Prediccion::create($row);
                $count = $count + 1;
            }
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function post(Request $request)
    {
        try
        {
            $data = collect($request->input('items'))->map(function ($item) {
                return new Prediccion([
                    'hora' => $item['hora'],
                    'dia' => $item['dia'],
                    'velocidad_viento' => $item['velocidad_viento'],
                    'direccion_viento' => $item['direccion_viento'],
                    'temperatura' => $item['temperatura'],
                    'humedad' => $item['humedad'],
                    'probabilidad_lluvia' => $item['probabilidad_lluvia'],
                    'detalle' => $item['detalle'],
                    'indice_uv' => $item['indice_uv'],
                    'descripcion' => $item['descripcion'],
                    'maximo' => $item['maximo'],
                    'minimo' => $item['minimo']
                ]);
            });

            $count = $this->save_new_forecasts($data);
            return response()->json([
                'message' => 'Se creo '.$count.' predicciones.'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Se debe enviar una ubicacion disponible.'
            ], 404);
        }
        
    }
}
