<?php

use App\Http\Controllers\ObservadorController;
use App\Http\Controllers\PrecipitacionController;
use App\Models\Observador;
use App\Models\TipoObservador;
use App\Models\TipoObservadorCategoria;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\Route;
use App\Models\Departamento;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;


Route::get('/departamentos', function () {
    return response()->json(ApiHelper::getAlloweds(Departamento::class), 200);
});

Route::get('/ubicaciones', function () {
    return response()->json(ApiHelper::getAlloweds(Ubicacion::class), 200);
});

Route::get('/ubicaciones/{id}', function ($id) {
    $ubicacion = Ubicacion::findOrFail($id);
    if ($ubicacion->estado === 'B') {
        return response()->json([
            'message' => 'Observador no disponible'
        ], 404);
    }
    return response()->json($ubicacion, 200);
});

Route::get('/tipos_observadores', function () {
    return response()->json(ApiHelper::getAlloweds(TipoObservador::class), 200);
});
Route::get('/tipos_observadores_categoria', function () {
    return response()->json(ApiHelper::getAlloweds(TipoObservadorCategoria::class), 200);
});
Route::get('/', function () {
    return view('welcome');
});

Route::get('/token', function () {
    return csrf_token(); 
});


Route::get('/observadores', [ObservadorController::class, 'getAll']);
Route::get('/observadores_completo', [ObservadorController::class, 'getWithValues']);
Route::post('/observadores', [ObservadorController::class, 'store']);
Route::put('/observadores/{id}', [ObservadorController::class, 'update']);
Route::put('/observadores_files/{id}', [ObservadorController::class, 'update_with_files']);
Route::delete('/observadores/{id}', [ObservadorController::class, 'destroy']);


Route::get('/precipitaciones', [PrecipitacionController::class, 'getAll']);
Route::post('/precipitaciones', [PrecipitacionController::class, 'store']);
Route::put('/precipitaciones/{id}', [PrecipitacionController::class, 'update']);
Route::delete('/precipitaciones/{id}', [PrecipitacionController::class, 'destroy']);
