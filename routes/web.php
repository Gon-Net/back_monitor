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
use App\Http\Controllers\ContadorController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\PrediccionController;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateSanctum;

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

    Route::get('/login', function(){
        return AuthenticateSanctum::no_logged();
    });

    Route::get('/departamentos', [DepartamentosController::class, 'getAll']);
    Route::get('/tipos_frecuencia', [TiposFrecuenciaController::class, 'getAll']);
    Route::get('/tipos_observadores_categoria', [TiposObservadoresCategoriaController::class, 'getAll']);
    Route::get('/tipos_observadores', [TipoObservadoresController::class, 'getAll']);
    
    Route::get('/ubicaciones', [UbicacionesController::class, 'getAll']);
    Route::get('/ubicaciones/{id}', [UbicacionesController::class, 'getOne']);
    
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/observadores', [ObservadorController::class, 'getAll']);
    Route::get('/observadores_completo', [ObservadorController::class, 'getWithValues']);
    Route::post('/observadores-buscar', [ObservadorController::class, 'findObservador']);
    
    Route::get('/eventos-extremos', [EventoExtremoController::class, 'getAll']);
    Route::get('/eventos-extremos/{id}', [EventoExtremoController::class, 'getOne']);
    Route::post('/eventos-extremos', [EventoExtremoController::class, 'store']);
    Route::put('/eventos-extremos/{id}', [EventoExtremoController::class, 'update']);
    Route::delete('/eventos-extremos/{id}', [EventoExtremoController::class, 'destroy']);

    Route::get('/microestacion', [MicroEstacionController::class, 'getAll']);

    Route::get('/mem', [MemController::class, 'getAll']);
    Route::get('/migrate', [MemController::class, 'migrate']);
    Route::get('/migrate-station-day', [MemController::class, 'migratePerDay']);
    Route::get('/migrate-day', [MemController::class, 'migrateOneDay']);

    Route::get('/visitas', [VisitaController::class, 'getAll']);
    Route::post('/visitas', [VisitaController::class, 'store']);
    Route::put('/visitas/{id}', [VisitaController::class, 'update']);
    Route::delete('/visitas/{id}', [VisitaController::class, 'destroy']);

    Route::get('/contadores', [ContadorController::class, 'getAll']);
    Route::post('/contadores', [ContadorController::class, 'store']);

    Route::get('/pronosticos', [PrediccionController::class, 'getAll']);
    Route::get('/migrar-pronosticos', [PrediccionController::class, 'migrate']);

    Route::get('/precipitaciones', [PrecipitacionController::class, 'getAll']);
    Route::get('/precipitaciones/ubicaciones-faltantes/{date}', [PrecipitacionController::class, 'getAusentUbicationsPerDate']);
    Route::post('/precipitaciones', [PrecipitacionController::class, 'store']);
    Route::put('/precipitaciones/{id}', [PrecipitacionController::class, 'update']);
    Route::delete('/precipitaciones/{id}', [PrecipitacionController::class, 'destroy']);

    Route::middleware(['auth:sanctum', 'check.token.expiry'])->group(function () {
        Route::post('/observadores', [ObservadorController::class, 'store']);
        Route::put('/observadores/{id}', [ObservadorController::class, 'update']);
        Route::put('/observadores_files/{id}', [ObservadorController::class, 'update_with_files']);
        Route::delete('/observadores/{id}', [ObservadorController::class, 'destroy']);
    });
});
