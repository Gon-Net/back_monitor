<?php

namespace App\Http\Controllers;
use App\Models\Ubicacion;
use App\Models\Contador;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Validation\ValidationException;

class ContadorController extends Controller
{
    public function getAll(Request $request)
    {
        $date = $request->input('fecha');
        $perPage = $request->input('items', 100);
        $countersFiltered = ApiHelper::getAlloweds(Contador::class, $perPage)->pluck('id');
        $counters = Contador::whereIn('id', $countersFiltered);
        if ($date !== null){
            $counters = $counters->whereDate('fecha_registro', $date);
        }

        $counters = $counters
            ->paginate($perPage);
        return response()->json($counters, 200);
    }
    public function getOne($id)
    {
        $counter = Contador::findOrFail($id);
        if ($counter->estado === 'B') {
            return response()->json([
                'message' => 'Contador no disponible'
            ], 404);
        }
        return response()->json($counter, 200);
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'fecha_registro' => 'required|date',
                'contador' => 'required|numeric',
                'observacion' => 'required|string|max:100'
            ]);
            $visit = Contador::create($validated);
            return response()->json([
                'message' => 'Contador creado exitosamente',
                'data' => $visit,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al crear.',
                'errors' => $e->errors(),
            ], 404);
        }
    }
    
}
