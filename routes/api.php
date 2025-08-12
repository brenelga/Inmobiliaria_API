<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FacturacionController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\RegistroController;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/registro', [RegistroController::class, 'registrar']);
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::middleware(['auth.mongodb'])->group(function () {
    Route::get('/perfil', [UserController::class, 'perfil']);
    Route::get('/vehiculos', [UserController::class, 'vehiculos']);

    Route::prefix('facturacion')->group(function () {
        Route::get('/', [FacturacionController::class, 'obtenerDatosFacturacion']);
        Route::post('/', [FacturacionController::class, 'guardarDatosFacturacion']);
        Route::get('/estados', [FacturacionController::class, 'obtenerEstados']);
    });

    Route::prefix('vehiculos')->group(function () {
        Route::get('/', [VehiculoController::class, 'index']);
        Route::post('/', [VehiculoController::class, 'store']);
        Route::put('/{vehiculo}', [VehiculoController::class, 'update']); // Usa Route Model Binding
        Route::delete('/{vehiculo}', [VehiculoController::class, 'destroy']);
    });

    Route::prefix('metodos-pago')->group(function () {
        Route::get('/', [MetodoPagoController::class, 'index']);
        Route::post('/', [MetodoPagoController::class, 'store']);
        Route::delete('/{metodoPago}', [MetodoPagoController::class, 'destroy']); // Usa Route Model Binding
    });

    Route::prefix('usuario')->group(function () {
        Route::get('/', [UserController::class, 'show']);
        Route::patch('/datos-personales', [UserController::class, 'updatePersonalData']);
        Route::patch('/credenciales', [UserController::class, 'updateCredentials']);
        Route::post('/correos', [UserController::class, 'addEmail']); // Corrección: estaba mal escrito
        Route::delete('/correos/{email}', [UserController::class, 'removeEmail']);
        Route::post('/telefonos', [UserController::class, 'addPhone']);
        Route::delete('/telefonos/{phone}', [UserController::class, 'removePhone']);
    });
});