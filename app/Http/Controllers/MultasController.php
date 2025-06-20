<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Multa;
use Carbon\Carbon;

class MultasController extends Controller
{
    public function obtenerMultas($departamentoId) {
        return Multa::where('departamento_id', $departamentoId)
        ->orderBy('fecha', 'desc')
        ->get();
    }
    
    public function store(Request $request) {
        $request->validate([
            'departamento_id' => 'required|string',
            'monto' => 'required|numeric',
            'mensaje' => 'required|string',
        ]);

        $multa = Multa::create([
            'departamento_id' => $request->departamento_id,
            'mensaje' => $request->mensaje,
            'monto' => $request->monto,
            'fecha' => Carbon::now()->format('Y-m-d'),
            'status' => 'Sin Pagar',
            'read' => 'unread'
        ]);

        return response()->json($multa, 201);
    }
}
