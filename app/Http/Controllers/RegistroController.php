<?php

namespace App\Http\Controllers;

use App\Models\UsuarioModel;
use App\Models\VehiculoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\UTCDateTime;

class RegistroController extends Controller
{
    public function registrar(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'nombre_usuario.nombre' => 'required|string|max:100',
            'nombre_usuario.ap_pat' => 'required|string|max:100',
            'nombre_usuario.ap_mat' => 'nullable|string|max:100',
            'credenciales.usuario' => 'required|string|max:50|unique:Usuario,nombre_usuario.usuario',
            'credenciales.contrasena' => 'required|string|min:8',
            'correo.0' => 'required|email|unique:Usuario,correo',
            'telefono.0' => 'required|string|max:15',
            'vehiculo.marca' => 'required|string|max:50',
            'vehiculo.modelo' => 'required|string|max:50',
            'vehiculo.placas' => 'required|string|max:10|unique:Vehiculo,placas',
            'vehiculo.VIN' => 'required|string|max:17|unique:Vehiculo,vin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Procesar contraseña: Vigenère + Hash
        $password = $this->procesarContrasena(
            $request->input('credenciales.contrasena'),
            $request->input('credenciales.usuario')
        );

        // Estructura completa del usuario
        $usuarioData = [
        'nombre_usuario' => [
            'nombre' => $request->input('nombre_usuario.nombre'),
            'ap_pat' => $request->input('nombre_usuario.ap_pat'),
            'ap_mat' => $request->input('nombre_usuario.ap_mat') ?? '',
            'usuario' => $request->input('credenciales.usuario'),
            'tipo' => 'Usuario'
        ],
        'contrasena' => $password,
        'correo' => (array)$request->input('correo.0'), // Forzar array
        'telefono' => (array)$request->input('telefono.0'), // Forzar array
        'datos_pago' => [], // Array vacío
        'facturacion' => null, // Null explícito
        '__v' => 0
    ];

    
        $usuario = UsuarioModel::create($usuarioData);

        $vehiculoData = [
            'marca' => strtoupper($request->input('vehiculo.marca')),
            'modelo' => strtoupper($request->input('vehiculo.modelo')),
            'placas' => strtoupper($request->input('vehiculo.placas')),
            'color' => 'BLANCO',
            'anio' => (int) date('Y'),
            'vin' => strtoupper($request->input('vehiculo.VIN')),
            'propietario' => $usuario->_id,
            'updated_at' => new UTCDateTime(now()->getTimestamp() * 1000),
            'created_at' => new UTCDateTime(now()->getTimestamp() * 1000)
        ];

        $vehiculo = VehiculoModel::create($vehiculoData);

        $usuario->refresh();

        // Preparar respuesta
        return response()->json([
            'message' => 'Registro exitoso',
            'usuario' => $usuario,
            'vehiculo' => $vehiculo
        ], 201);
    }

    private function procesarContrasena($password, $username)
    {
        try {
            $response = Http::post('https://api-python-sage.vercel.app/vigenere/cifrar', [
                'texto' => $password,
                'clave' => $username
            ]);
            
            if ($response->successful()) {
                $vigenerePassword = $response->json()['resultado'];
                return Hash::make($vigenerePassword);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}