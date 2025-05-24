<?php

namespace App\Http\Controllers;
use App\Models\Forecast;
use App\Models\MicroEstacion;
use App\Models\Prediccion;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
class PrediccionController extends Controller
{
    public function getAll(Request $request)
    {
        $ubication_id = $request->input('ubication_id', null);
        $date = $request->input('fecha_registro', null);
        if ($ubication_id == null){
            return response()->json([
                'message' => 'Se debe enviar una ubication_id'
            ], 404);
        }
        if ($date == null){
            return response()->json([
                'message' => 'Se debe enviar una fecha_registro en formato yyyy-mm-dd'
            ], 404);
        }
        $date = Carbon::parse($date)->toDateString();

        $predictions_filtered = ApiHelper::getAlloweds(Prediccion::class, all: true)->pluck('id')
            ->filter() 
            ->values();
        
        $predictions = Prediccion::whereIn('id', $predictions_filtered)
            ->where('ubicacion_id', $ubication_id)
            ->whereDate('fecha_registro', $date)
            ->paginate(20);
            
        return response()->json($predictions, 200);
    }
    private function save_new_forecasts($data){
        DB::beginTransaction();
        try {
            $rules = [
                'ubicacion_id' => 'required|numeric',
                //'ubicacion_id' => 'required|exists:ubicacion,id',
                'hora' => 'required|numeric',
                'dia' => 'required|string|max:20',
                'velocidad_viento' => 'nullable|numeric',
                'direccion_viento' => 'nullable|string|max:10',
                'temperatura' => 'nullable|numeric',
                'humedad' => 'nullable|numeric',
                'probabilidad_lluvia' => 'required|numeric',
                'detalle' => 'required|string',
                'indice_uv' => 'required|numeric',
                'descripcion' => 'required|string',
                'maximo' => 'required|numeric',
                'minimo' =>  'required|numeric',
                'fecha_pronostico' => 'required|date',
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
                $newValues = [];
                foreach ($data as &$p) {
                    $value = [
                        'ubicacion_id' => $station_id,
                        'probabilidad_lluvia' => $p["ProbabilidadLluvia"],
                        'indice_uv' => $p['IndiceUV'],
                        'detalle' => $p['Detalle'],
                        'descripcion' => $p["DescripcionUV"],
                        'maximo' => $p["max"],
                        'minimo' => $p["min"],
                        'velocidad_viento' => $p["VelocidadViento"],
                        'direccion_viento' => $p["DireccionViento"],
                        'temperatura' => $p["Temperatura"],
                        'humedad' => $p["Humedad"],
                        'hora' =>$p["hora"],
                        'dia' =>$p["dia"],
                        'fecha_pronostico' => $p['fecha']
                    ];
                    $newValues[] = $value;
                }
                return PrediccionController::save_new_forecasts($newValues);
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
        $count = 0;
        try {
            $ubicacions_id = ApiHelper::getAlloweds(MicroEstacion::class, all: true)->pluck('id_pem')->toArray();
            $date = date("Y-m-d");
            $registrados = Prediccion::whereDate('fecha_registro', $date)
                ->whereIn('ubicacion_id', $ubicacions_id)
                ->pluck('ubicacion_id')
                ->toArray();
            $ubicacions_id = array_diff($ubicacions_id, $registrados);
            
            foreach($ubicacions_id as $uid){
                $count = $count + PrediccionController::migrate_forecasts_per_station($uid);
                sleep(2);
            }
            return $count;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function migrate()
    {
        try{
            $count = $this->migrateForecasts();
            return response()->json([
                'message' => 'Se guardo '.$count.' nuevos pronosticos.'
            ], 200);
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Ocurrio un error.'
            ], 404);
        }
    }
}
