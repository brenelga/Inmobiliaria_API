<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Multa;
class MultasController extends Controller
{
    public function obtenerMultas($departamentoId) {
        return Multa::where('departamento_id', $departamentoId)
        ->orderBy('fecha', 'desc')
        ->get();
    }
}
