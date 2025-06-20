<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Multa;
use App\Http\Controllers\MarkAsReadController;
use App\Http\Controllers\MultasController;
use App\Http\Controllers\LoginController;

Route::middleware(['auth:sanctum'])->group(function() {

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/multas', [MultasController::class, 'store']);
    });

    Route::middleware(['role:inquilino'])->group(function () {
        Route::get('/notificaciones/{id}', function ($id) {
            $multas = Multa::where('departamento_id', $id)->get();
            return response()->json([
                'success' => true,
                'data' => $multas
            ]);
        });

        Route::post('/mark_as_read', [MarkAsReadController::class, 'markAllAsRead']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'login']);