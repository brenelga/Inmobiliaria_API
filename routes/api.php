<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Multa;
use App\Http\Controllers\MarkAsReadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/notificaciones/{departamentoId}', function ($departamentoId) {
    return response()->json(
        Multa::where('departamento_id', (string)$departamentoId)
            ->orderBy('fecha', 'desc')
            ->get()
    );
});

Route::post('/mark_as_read', [MarkAsReadController::class, 'markAsRead']);
