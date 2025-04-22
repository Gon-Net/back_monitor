<?php

namespace App\Http\Controllers;
use App\Models\Mem;
use App\Models\MicroEstacion;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
class MemController extends Controller
{
    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'id_pem' => 'required|exists:microestacion,id_pem',
                'fecha' => 'required|date',
                'hora' => 'required|numeric',
                'temperatura' => 'nullable|numeric',
                'humedad' => 'required|numeric',
                'presion' => 'required|numeric',
                'uv' => 'required|numeric',
                'precipitacion_tipo' => 'nullable|string',
                'precipitacion_probabilidad' => 'required|numeric',
                'precipitacion' => 'required|numeric'
            ]);
            Mem::create($validated);
            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }
    private function get_keys($data)
    {
        $keys = array();
        foreach($data as $item){
            $id = $item['id_pem'];
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
        $currents = ApiHelper::getAlloweds(Mem::class, all: true);
        $all_keys = MemController::get_keys(data: $currents);
        $dif_keys = array_diff($keys, $all_keys);
        foreach ($dif_keys as $key) {
            array_push($indexs, array_search($key, $keys));
        }
        return $indexs;
    }
    private function save_new_mems($data){
        DB::beginTransaction();
        try {
            $rules = [
                'id_pem' => 'required|exists:microestacion,id_pem',
                'fecha' => 'required|date',
                'hora' => 'required|numeric',
                'temperatura' => 'nullable|numeric',
                'humedad' => 'required|numeric',
                'presion' => 'required|numeric',
                'uv' => 'required|numeric',
                'precipitacion_tipo' => 'nullable|string',
                'precipitacion_probabilidad' => 'required|numeric',
                'precipitacion' => 'required|numeric'
            ];
            $count = 0;
            foreach ($data as $row) {
                $validator = Validator::make($row, $rules);
                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first());
                }
                Mem::create($row);
                $count = $count + 1;
            }
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    private function migratePerStationAndDate($station_id, $date)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDEzNTgyMjcsImV4cCI6MTgwNDQzMDIyN30.bErK9dnooNLn45MOzOEKNxq2Epbq3usdTTjWivbVvUk',
                'Accept' => 'application/json',
            ])->get("https://sr.info.bo/pro/dato_fecha/{$station_id}/{$date}");
            if ($response->successful()) {
                $datos = $response->json();
                $data = $datos['DATA'];
                $new_indexs = MemController::exclude_current_keys(MemController::get_keys($data));
                $new_objects = array();
                foreach ($new_indexs as $index){
                    array_push($new_objects, $data[$index]);
                }
                return MemController::save_new_mems($new_objects);
            } else {
                //$codigoError = $response->status();
                $mensajeError = $response->body();
                throw new \Exception($mensajeError);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function migratePerDay(Request $request)
    {
        $station_id = $request->input('estacion');
        $date = $request->input('fecha');
        if ($station_id == null){
            return response()->json(['error' => 'Debes agregar un id en estacion'], 400);
        }
        if ($date == null){
            return response()->json(['error' => 'Debes agregar una fecha'], 400);
        }
        try {
            $count = MemController::migratePerStationAndDate($station_id, $date);
            return response()->json([
                'exito' => "Se creo {$count} nuevos elementos."
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear.',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
    public function migrate_all()
    {
        try {
            $count = 0;
            $stations_id = ApiHelper::getAlloweds(MicroEstacion::class, all: true)->pluck('id_pem');
            $init = Carbon::create(2025, 4, 3);
            $now = Carbon::now();
            $periods = CarbonPeriod::create($init, '1 day', $now);
            $dates = [];
            foreach ($periods as $date) {
                array_push($dates, $date->format('Y-m-d'));
            }
            foreach($stations_id as $station_id){
                foreach($dates as $date){
                    $count = $count + MemController::migratePerStationAndDate($station_id, $date);
                }
            }
            return $count;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    
    public function migratePerDate($date)
    {
        try {
            $count = 0;
            $stations_id = ApiHelper::getAlloweds(MicroEstacion::class, all: true)->pluck('id_pem');
            foreach($stations_id as $station_id){
                $count = $count + MemController::migratePerStationAndDate($station_id, $date);
            }
            return $count;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function migrateOneDay(Request $request)
    {
        $date = $request->input('fecha') ?? now();
        try {
            $count = MemController::migratePerDate($date);
            return response()->json([
                'exito' => "Se creo {$count} nuevos elementos."
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear.',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
    public function migrate()
    {
        try {
            $count = MemController::migrate_all();
            return response()->json([
                'exito' => "Se creo {$count} nuevos elementos."
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear.',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        $page = $request->input('page', 1);
        
        $allowedIds = ApiHelper::getAlloweds(Mem::class, $perPage,true)->pluck('id');
        
        $mems = Mem::whereIn('id', $allowedIds)
            ->with(['microestacion'])
            ->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json($mems, 200);
    }
        
}