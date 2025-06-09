<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Multa;
use App\Http\Controllers\MarkAsReadController;
use App\Http\Controllers\MultasController;

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

use Illuminate\Support\Facades\Log;

Route::get('/notificaciones/{id}', function ($id) {
    $multas = Multa::where('departamento_id', $id)->get();
    return response()->json([
        'success' => true,
        'data' => $multas
    ]);
});


Route::post('/mark_as_read',  [MarkAsReadController::class, 'markAllAsRead']);
Route::post('/multas', [MultasController::class, 'store']);