<?php

namespace App\Http\Controllers;

use App\Models\UsuarioModel;
use App\Models\VehiculoModel;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\VigenereEncryptionService;
use Illuminate\Support\Facades\Hash;




class UserController extends Controller
{
    /**
     * Obtener el perfil del usuario autenticado
     */
    public function perfil(Request $request)
{
    $user = $request->user();
    
    if (!$user) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    return response()->json([
        'nombre_usuario' => [
            'nombre' => $user['nombre_usuario']['nombre'] ?? '',
            'ap_pat' => $user['nombre_usuario']['ap_pat'] ?? '',
            'ap_mat' => $user['nombre_usuario']['ap_mat'] ?? '',
            'usuario' => $user['nombre_usuario']['usuario'] ?? '', // Añadido
            'tipo' => $user['nombre_usuario']['tipo'] ?? 'Usuario' // Añadido
        ],
        'credenciales' => [ // Nueva sección
            'usuario' => $user['nombre_usuario']['usuario'] ?? '',
            'contrasena' => '' // No enviar la contraseña real
        ],
        'correo' => isset($user['correo']) ? (is_array($user['correo']) ? $user['correo'] : [$user['correo']]) : [],
        'telefono' => isset($user['telefono']) ? (is_array($user['telefono']) ? $user['telefono'] : [$user['telefono']]) : [],
        'fecha_registro' => $user['created_at'] ?? null
    ]);
}

    public function vehiculos()
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $vehiculos = VehiculoModel::where('propietario', $usuario->_id)->get();

        return response()->json($vehiculos);
    }

    public function show(Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    public function updatePersonalData(Request $request) {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nombre_usuario.nombre' => 'required|string|max:50',
            'nombre_usuario.ap_pat' => 'nullable|string|max:50',
            'nombre_usuario.ap_mat' => 'nullable|string|max:50'
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->nombre_usuario = array_merge(
            (array)$user->nombre_usuario,
            $request->only('nombre_usuario')['nombre_usuario']
        );

        $user->save();
    }


public function updateCredentials(Request $request, VigenereEncryptionService $vigenere)
{
    $user = $request->user();
    
    $validator = Validator::make($request->all(), [
        'usuario' => [
            'required',
            'string',
            'max:30',
            Rule::unique('Usuario', 'nombre_usuario.usuario')->ignore($user->_id, '_id')
        ],
        'current_password' => 'required|string',
        'new_password' => 'nullable|string|min:8|different:current_password',
        'confirm_password' => 'required_with:new_password|same:new_password',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Descifrar la contraseña actual recibida con Vigenère
        $currentPasswordDecrypted = $vigenere->decrypt(
            $request->current_password, 
            $user->nombre_usuario['usuario']
        );

        // Verificar contraseña actual (comparando con el hash bcrypt)
        if (!Hash::check($currentPasswordDecrypted, $user->contrasena)) {
            return response()->json([
                'success' => false,
                'errors' => ['current_password' => ['La contraseña actual es incorrecta']]
            ], 422);
        }

        // Actualizar nombre de usuario
        $user->nombre_usuario = array_merge(
            (array)$user->nombre_usuario,
            ['usuario' => $request->usuario]
        );

        if ($request->new_password) {
            $newPasswordDecrypted = $vigenere->decrypt(
                $request->new_password, 
                $request->usuario // Usar el nuevo nombre de usuario como clave
            );
            
            // Aplicar bcrypt al resultado
            $user->contrasena = Hash::make($newPasswordDecrypted);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Credenciales actualizadas correctamente',
            'data' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error en el proceso de cifrado: ' . $e->getMessage()
        ], 500);
    }
}

public function addEmail(Request $request){
    $user = $request->user();

    $validator = Validator::make($request->all(), [
        'email' => [
            'required',
            'email',
            'max:100',
            Rule::unique('Usuario', 'correo')->where(function($query) use ($user) {
                return $query->where('_id', '<>', $user->_id);
            })
        ]
    ]);

    if($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    if(in_array($request->email, $user->correo)) {
        return response()->json([
            'success' => false,
            'errors' => ['email' => ['¡Este correo ya esta registrado!']]
        ], 422);
    }

    $user->push('correo', $request->email);

    return response()->json([
        'success' => true,
        'message' => 'Correo registrado correctamente',
        'data' => $user->correo
    ]);
}

    public function removeEmail(Request $request, $email){
        $user = $request->user();

        if(!in_array($email,$user->correo)){
            return response()->json([
                'success' => false,
                'message' => 'El correo no existe en los registros'
            ], 404);
        }

        if(count($user->correo) <= 1){
            return response()->json([
                'success' => false,
                'message' => 'Debes tener al menos un correo registrado'
            ], 422);
        }

        $user->pull('correo', $email);

        return response()->json([
            'success' => true,
            'message' => 'Correo eliminado correctamente',
            'data' => $user->correo
        ], 201);
    }

    public function addPhone(Request $request){
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('Usuario', 'telefono')->where(function($query) use ($user){
                    return $query->where('_id', '<>', $user->_id);
                })
            ]
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        if (strlen($phone) < 10){
            return response()->json([
                'success' => false,
                'errors' => ['phone' => ['El número de teléfono debe tener al menos 10 dígitos']]
            ], 422);
        }

        if(in_array($phone, $user->telefono)){
            return response()->json([
                'success' => false,
                'errors' => ['phone' => ['Este número ya está registrado']]
            ], 422);
        }

        $user->push('telefono', $phone);

        return response()->json([
            'success' => true,
            'message' => 'Número de teléfono agregado correctamente',
            'data' => $user->telefono
        ]);
    }

    public function removePhone(Request $request, $phone)
    {
        $user = $request->user();
        
        $phoneToRemove = preg_replace('/[^0-9]/', '', $phone);

        $phoneExists = false;
        foreach ($user->telefono as $userPhone) {
            if (preg_replace('/[^0-9]/', '', $userPhone) === $phoneToRemove) {
                $phoneExists = true;
                break;
            }
        }

        if (!$phoneExists) {
            return response()->json([
                'success' => false,
                'message' => 'El número de teléfono no existe en tus registros'
            ], 404);
        }

        if (count($user->telefono) <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Debes tener al menos un número de teléfono registrado'
            ], 422);
        }

        $user->pull('telefono', $phone);

        return response()->json([
            'success' => true,
            'message' => 'Número de teléfono eliminado correctamente',
            'data' => $user->telefono
        ]);
    }
}