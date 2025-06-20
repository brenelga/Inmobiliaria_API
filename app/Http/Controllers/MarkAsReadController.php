<?php
namespace App\Http\Controllers;

use App\Models\Multa;
use Illuminate\Http\Request;

class MarkAsReadController extends Controller
{
        public function markAllAsRead(Request $request)
{
    if (!$request->isJson()) {
        return response()->json([
            'success' => false,
            'message' => 'Content-Type must be application/json'
        ], 415);
    }

    $departamentoId = $request->input('departamento_id');
    
    if(empty($departamentoId)) {
        return response()->json([
            'success' => false,
            'message' => 'El campo departamento_id es requerido'
        ], 400);
    }

    if (is_array($departamentoId)) {
        $departamentoId = $departamentoId[0] ?? null;
    }

    try {
        $updatedCount = Multa::where('departamento_id', (string)$departamentoId)
            ->where('read', 'unread')
            ->update([
                'read' => 'read',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Multas marcadas como leídas',
            'updated_count' => $updatedCount
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar la solicitud',
            'error' => $e->getMessage()
        ], 500);
    }
}    
}
