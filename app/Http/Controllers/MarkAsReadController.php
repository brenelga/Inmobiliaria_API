<?php
namespace App\Http\Controllers;

use App\Models\Multa;
use Illuminate\Http\Request;

class MarkAsReadController extends Controller
{
    public function markAllAsRead(Request $request)
    {
        $request->validate([
            'departamento_id' => 'required|string'
        ]);

        try {
            $departamentoId = $request->input('departamento_id');

            $updatedCount = Multa::where('departamento_id', $departamentoId)
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
