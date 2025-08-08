<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioModel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MetodoPagoController extends Controller
{
    public function index() {
    $usuario = Auth::user();

    if (!$usuario) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    $metodosPago = collect($usuario->datos_pago ?? [])->map(function ($metodo) {
        $ultimosDigitos = substr($metodo['numero_tarjeta'] ?? '', -4);
        
        return [
            'id' => $metodo['id'] ?? Str::uuid()->toString(),
            'nombre_tarjeta' => $metodo['nombre_tarjeta'] ?? 'Sin nombre',
            'ultimos_digitos' => '•••• •••• •••• ' . $ultimosDigitos,
            'fecha_exp' => $metodo['fecha_exp'] ?? '00/00',
            'tipo' => $this->determinarTipoTarjeta($metodo['numero_tarjeta'] ?? '')
        ];
    });

    return response()->json($metodosPago);
}

    public function store(Request $request) {
    $validator = Validator::make($request->all(), [
        'nombre_tarjeta' => 'required|string|max:100',
        'numero_tarjeta' => 'required|string|min:13|max:19|regex:/^[0-9]+$/',
        'fecha_exp' => 'required|date_format:m/Y|after_or_equal:' . date('m/Y'),
        'cvv' => 'required|string|min:3|max:4|regex:/^[0-9]+$/'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $numeroEncriptado = $this->encriptarNumeroTarjeta($request->numero_tarjeta);

        $nuevoMetodo = [
            'id' => Str::uuid()->toString(),
            'nombre_tarjeta' => $request->nombre_tarjeta,
            'numero_tarjeta' => $numeroEncriptado,
            'fecha_exp' => $request->fecha_exp,
            'fecha_agregado' => now()->toDateTimeString()
        ];

        $usuario = Auth::user();
        $datosPago = $usuario->datos_pago ?? [];
        $datosPago[] = $nuevoMetodo;
        $usuario->datos_pago = $datosPago;
        $usuario->save();

        return response()->json([
            'message' => 'Método de pago guardado exitosamente',
            'metodo' => [
                'id' => $nuevoMetodo['id'],
                'ultimos_digitos' => '•••• •••• •••• ' . substr($request->numero_tarjeta, -4),
                'tipo' => $this->determinarTipoTarjeta($request->numero_tarjeta)
            ]
        ], 201);

    } catch (Exception $e) {
        return response()->json(['message' => 'Error al guardar el método'], 500);
    }
}

    public function destroy($id) {
        $usuario = Auth::user();

        if(!$usuario) {
            return response()->json(['message' => 'Usuario no Encontrado'], 401);
        }

        try {
            $datosPago = $usuario->datos_pago ?? [];
            $originalCount = count($datosPago);

            $datosPago = array_filter($datosPago, function($metodo) use ($id) {
                return ($metodo['id'] ?? null) !== $id;
            });

            if(count($datosPago) === $originalCount) {
                return response()->json(['message' => 'Método de pago no encontrado'], 404);
            }

            $usuario->datos_pago = array_values($datosPago);
            $usuario->save();

            return response()->json(['Método de Pago eliminado correctamente'], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el método de Pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function determinarTipoTarjeta($numero) {
        $numero = preg_replace('/\D/', '', $numero);

        if (preg_match('/^4/', $numero)) return 'visa';
        if (preg_match('/^5[1-5]/', $numero)) return 'mastercard';
        if (preg_match('/^3[47]/', $numero)) return 'amex';
        if (preg_match('/^6(?:011|5)/', $numero)) return 'discover';

        return 'unknown';
    }

    private function validarTarjeta($numero) {
        $numero = preg_replace('/\D/', '', $numero);

        $sum = 0;
        $alt = false;

        for ($i = strlen($numero) - 1; $i>=0; $i--) {
            $n = intval($numero[$i]);
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n = ($n % 10) + 1;
                }
            }

            $sum += $n;
            $alt = !$alt;
        }

        return ($sum % 10) === 0;
    }

    private function encriptarNumeroTarjeta($numero) {
        $numero = preg_replace('/\D/', '', $numero);
        $longitud = strlen($numero);
        $ultimos4 = substr($numero, -4);
        
        return str_repeat('*', $longitud - 4) . $ultimos4;
    }
}
