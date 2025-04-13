<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartamentosController;
use App\Http\Controllers\EventoExtremoController;
use App\Http\Controllers\MemController;
use App\Http\Controllers\MicroEstacionController;
use App\Http\Controllers\ObservadorController;
use App\Http\Controllers\PrecipitacionController;
use App\Http\Controllers\TipoObservadoresController;
use App\Http\Controllers\TiposFrecuenciaController;
use App\Http\Controllers\TiposObservadoresCategoriaController;
use App\Http\Controllers\UbicacionesController;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::get('/', function () {
        return "BIENVENIDO";
    });
    
    Route::get('/token', function () {
        return csrf_token(); 
    });

    Route::get('/phpinfo', function () {
        phpinfo();
    });

    Route::get('/departamentos', [DepartamentosController::class, 'getAll']);
    Route::get('/tipos_frecuencia', [TiposFrecuenciaController::class, 'getAll']);
    Route::get('/tipos_observadores_categoria', [TiposObservadoresCategoriaController::class, 'getAll']);
    Route::get('/tipos_observadores', [TipoObservadoresController::class, 'getAll']);
    Route::get('/ubicaciones', [UbicacionesController::class, 'getAll']);
    Route::get('/ubicaciones/{id}', [UbicacionesController::class, 'getOne']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/observadores', [ObservadorController::class, 'getAll']);
    Route::get('/observadores_completo', [ObservadorController::class, 'getWithValues']);
    Route::post('/observadores-buscar', [ObservadorController::class, 'findObservador']);

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
    Route::get('/migrate-station-day', [MemController::class, 'migratePerDay']);
    Route::get('/migrate-day', [MemController::class, 'migratePerDate']);
});
