<?php

namespace App\Http\Controllers;
use App\Models\Mem;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class MemController extends Controller
{
    private function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'id_pem' => 'required|numeric',
                'fecha' => 'required|date',
                'hora' => 'required|numeric',
                'temperatura' => 'required|numeric',
                'humedad' => 'required|numeric',
                'presion' => 'required|numeric',
                'uv' => 'required|numeric',
                'precipitacion_tipo' => 'required|string',
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
        $currents = ApiHelper::getAlloweds(Mem::class, all: true);
        $all_keys = MemController::get_keys($currents);
        return array_diff($keys, $all_keys);
    }
    private function save_new_mems($data){
        DB::beginTransaction();
        try {
            $rules = [
                'id_pem' => 'required|numeric',
                'fecha' => 'required|date',
                'hora' => 'required|numeric',
                'temperatura' => 'required|numeric',
                'humedad' => 'required|numeric',
                'presion' => 'required|numeric',
                'uv' => 'required|numeric',
                'precipitacion_tipo' => 'required|string',
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
            return response()->json(['message' => "{$count} registros guardados exitosamente"], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function migrate(Request $request)
    {
        $perPage = $request->input('items', 100);
        $response = Http::withHeaders([
            'Authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDEzNTgyMjcsImV4cCI6MTgwNDQzMDIyN30.bErK9dnooNLn45MOzOEKNxq2Epbq3usdTTjWivbVvUk',
            'Accept' => 'application/json',
        ])->get('https://sr.info.bo/pro/dato_fecha/4/2025-04-06');
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
            $codigoError = $response->status();
            $mensajeError = $response->body();
            return response()->json($mensajeError, $codigoError);
        }
    }
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(Mem::class, $perPage), 200);
    }
}
