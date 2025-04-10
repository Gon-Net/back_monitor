<?php

namespace App\Http\Controllers;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;

class UbicacionesController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(Ubicacion::class, $perPage), 200);
    }
    public function getOne(Request $request, $id)
    {
        $ubicacion = Ubicacion::findOrFail($id);
        if ($ubicacion->estado === 'B') {
            return response()->json([
                'message' => 'Ubicacion no disponible'
            ], 404);
        }
        return response()->json($ubicacion, 200);
    }
}
