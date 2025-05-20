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
use Illuminate\Support\Facades\Http;
class PrediccionController extends Controller
{
    private function get_keys($data)
    {
        $keys = array();
        foreach($data as $item){
            $id = $item['ubicacion_id'];
            $date = $item['fecha'];
            $hour = $item['hora'];
            $key = "{$id}-{$date}-{$hour}";
            array_push($keys, $key);
        }
        return $keys;
    }
    #get the index of array keys that are not in database
    private function exclude_current_keys($keys)
    {
        $indexs = array();
        $currents = ApiHelper::getAlloweds(Prediccion::class, all: true);
        $all_keys = PrediccionController::get_keys(data: $currents);
        $dif_keys = array_diff($keys, $all_keys);
        foreach ($dif_keys as $key) {
            array_push($indexs, array_search($key, $keys));
        }
        return $indexs;
    }
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
                'ubicacion_id' => 'required|exists:ubicacion,id',
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
    public function migrate_forecasts_per_station($station_id)
    {
        try{
            $response = Http::withHeaders([
                'Authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDEzNTgyMjcsImV4cCI6MTgwNDQzMDIyN30.bErK9dnooNLn45MOzOEKNxq2Epbq3usdTTjWivbVvUk',
                'Accept' => 'application/json',
            ])->get("https://sr.info.bo/pro/pronosticos_pem/{$station_id}");
            if ($response->successful()) {
                $datos = $response->json();
                $data = $datos['DATA'];
                $new_indexs = Prediccion::exclude_current_keys(PrediccionController::get_keys($data));
                $new_objects = array();
                foreach ($new_indexs as $index){
                    array_push($new_objects, $data[$index]);
                }
                return PrediccionController::save_new_forecasts($new_objects);
            } else {
                //$codigoError = $response->status();
                $mensajeError = $response->body();
                throw new \Exception($mensajeError);
            }
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Ocurrio un error.'
            ], 404);
        }
    }
    public function migrateForecasts()
    {
        try{
            $count = 0;
            $ubicacions_id = ApiHelper::getAlloweds(Ubicacion::class, all: true)->pluck('id_pem');
            foreach($ubicacions_id as $uid){
                $count = $count + PrediccionController::migrate_forecasts_per_station($uid);
            }
            return response()->json([
                'message' => 'Se guardo '.$count.' pronosticos.'
            ], 200);
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Ocurrio un error.'
            ], 404);
        }
    }
}
