<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehiculoModel;
use App\Models\UsuarioModel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VehiculoController extends Controller
{
    public function index() {
        $usuario = Auth::user();

        if(!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 401);
        }

        $vehiculos = VehiculoModel::where('propietario', $usuario->_id)->get();
        
        return response()->json($vehiculos);
    }

    public function store(Request $request) {
        $usuario = Auth::user();

        if(!$usuario) {
            return response()->json(['message' => 'Usuario no Encontrado'], 401);
        }

        $validator = Validator::make($request->all(), [
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'placas' => 'required|string|max:15|unique:Vehiculo',
            'color' => 'nullable|string|max:30',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => 'nullable|string|max:17|unique:Vehiculo'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $vehiculo = new VehiculoModel([
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'placas' => strtoupper($request->placas),
                'color' => $request->color,
                'anio' => $request->anio,
                'vin' => $request->vin ? strtoupper($request->vin) : null,
                'propietario' => $usuario->_id
            ]);

            $vehiculo->save();

            return response()->json([
                'message' => 'Vehiculo registrado correctamente',
                'Vehiculo' => $vehiculo
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el vehículo',
                'Error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        $usuario = Auth::user();

        if(!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $vehiculo = VehiculoModel::where('_id', $id)->where('propietario', $usuario->_id)->first();

        if(!$vehiculo) {
            return response()->json(['message' => 'Vehiculo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'placas' => 'required|string|max:15|unique:Vehiculo,placas,'.$id.',_id',
            'color' => 'nullable|string|max:30',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => 'nullable|string|max:17|unique:Vehiculo,vin,'.$id.',_id'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $vehiculo->update([
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'placas' => strtoupper($request->placas),
                'color' => $request->color,
                'anio' => $request->anio,
                'vin' => $request->vin ? strtoupper($request->vin) : null
            ]);

            return response()->json([
                'message' => 'Vehiculo actualizado correctamente',
                'Vehiculo' => $vehiculo
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el vehiculo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id) {
        $usuario = Auth::user();

        if(!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $vehiculo = VehiculoModel::where('_id', $id)->where('propietario', $usuario->_id)->first();

        if(!$vehiculo) {
            return response()->json(['message' => 'Vehiculo no encontrado'], 404);
        }

        try {
            $vehiculo->delete();

            return response()->json([
                'message' => 'Vehiculo eliminado correctamente'
            ], 201);
        } catch (Exception $e) {
            return response() ->json([
                'message' => 'Error al eliminar el vehículo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
