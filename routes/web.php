<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MicroEstacionController;
use App\Http\Controllers\ObservadorController;
use App\Http\Controllers\PrecipitacionController;
use App\Http\Controllers\EventoExtremoController;
use App\Http\Controllers\MemController;
use App\Models\TipoFrecuencia;
use App\Models\TipoObservador;
use App\Models\TipoObservadorCategoria;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\Route;
use App\Models\Departamento;
use App\Helpers\ApiHelper;
use Illuminate\Foundation\Configuration\Middleware;

Route::group(['prefix' => 'v1'], function () {
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
    Route::get('/tipos_frecuencia', function () {
        return response()->json(ApiHelper::getAlloweds(TipoFrecuencia::class), 200);
    });
    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::get('/token', function () {
        return csrf_token(); 
    });
    
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
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
    
    Route::get('/eventos-extremos', [EventoExtremoController::class, 'getAll']);
    Route::get('/eventos-extremos/{id}', [EventoExtremoController::class, 'getOne']);
    Route::post('/eventos-extremos', [EventoExtremoController::class, 'store']);
    Route::put('/eventos-extremos/{id}', [EventoExtremoController::class, 'update']);
    Route::delete('/eventos-extremos/{id}', [EventoExtremoController::class, 'destroy']);

    Route::get('/microestacion', [MicroEstacionController::class, 'getAll']);

    Route::get('/mem', [MemController::class, 'getAll']);
    Route::get('/migrate', [MemController::class, 'migrate']);
});
