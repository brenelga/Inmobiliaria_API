<?php

namespace App\Http\Controllers;

use App\Models\UsuarioModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FacturacionController extends Controller
{
    // Obtener datos de facturación del usuario
    public function obtenerDatosFacturacion()
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        return response()->json([
            'facturacion' => $usuario->facturacion ?? []
        ]);
    }

    // Guardar/actualizar datos de facturación
    public function guardarDatosFacturacion(Request $request)
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $validator = Validator::make($request->all(), [
            'calle' => 'required|string|max:100',
            'colonia' => 'required|string|max:100',
            'num_ext' => 'required|string|max:10',
            'num_int' => 'nullable|string|max:10',
            'estado' => 'required|string|max:50',
            'municipio' => 'required|string|max:50',
            'cp' => 'required|digits:5',
            'rfc' => 'required|string|size:13|regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/',
            'regimen' => 'required|array',
            'regimen.*' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $datosFacturacion = [
                'calle' => $request->calle,
                'colonia' => $request->colonia,
                'num_ext' => $request->num_ext,
                'num_int' => $request->num_int,
                'estado' => $request->estado,
                'municipio' => $request->municipio,
                'cp' => $request->cp,
                'rfc' => strtoupper($request->rfc),
                'regimen' => $request->regimen
            ];

            $usuario->facturacion = $datosFacturacion;
            $usuario->save();

            return response()->json([
                'message' => 'Datos de facturación guardados correctamente',
                'facturacion' => $usuario->facturacion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar los datos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener listado de estados de México
    public function obtenerEstados()
    {
        $estados = [
            'Aguascalientes',
            'Baja California',
            'Baja California Sur',
            'Campeche',
            'Chiapas',
            'Chihuahua',
            'Ciudad de México',
            'Coahuila',
            'Colima',
            'Durango',
            'Estado de México',
            'Guanajuato',
            'Guerrero',
            'Hidalgo',
            'Jalisco',
            'Michoacán',
            'Morelos',
            'Nayarit',
            'Nuevo León',
            'Oaxaca',
            'Puebla',
            'Querétaro',
            'Quintana Roo',
            'San Luis Potosí',
            'Sinaloa',
            'Sonora',
            'Tabasco',
            'Tamaulipas',
            'Tlaxcala',
            'Veracruz',
            'Yucatán',
            'Zacatecas'
        ];

        return response()->json($estados);
    }
}