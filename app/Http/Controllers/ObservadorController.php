<?php

namespace App\Http\Controllers;

use App\Models\Observador;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
class ObservadorController extends Controller
{
    public function getWithValues()
    {
        $observadores = Observador::with(['ubicacion', 'tipoObservador', 'tipoObservadorCategoria'])->get();
        
        return response()->json($observadores, 200);
    }
    public function getAll()
    {
        return response()->json(ApiHelper::getAlloweds(Observador::class), 200);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ubicacion_id' => 'required|exists:ubicacion,id',
            'tipo_observador_id' => 'required|exists:tipo_observador,id',
            'tipo_observador_categoria_id' => 'required|exists:tipo_observador_categoria,id',
            'nombre_observador' => 'required|string|max:255',
            'numero_documento_identidad' => 'required|string|max:12',
            'fecha_nacimiento' => 'required|date',
            'numero_celular' => 'required|string|max:12',
            'correo' => 'required|email',
            'nombre_usuario' => 'required|string|max:255',
            'dir_documento_identidad' => 'nullable|file|mimes:jpg,png|max:2048', 
            'dir_acta_nombramiento' => 'nullable|file|mimes:jpg,png|max:2048'
        ]);

        if ($request->hasFile('dir_documento_identidad')) {
            $file = $request->file('dir_documento_identidad');
            $filename = time() . '_' . $file->getClientOriginalName(); 
            $file->move(public_path('uploads/documentos'), $filename);
            $validated['dir_documento_identidad'] = 'uploads/documentos/' . $filename;
        }
    
        if ($request->hasFile('dir_acta_nombramiento')) {
            $file = $request->file('dir_acta_nombramiento');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/actas'), $filename);
            $validated['dir_acta_nombramiento'] = 'uploads/actas/' . $filename;
        }

        $observador = Observador::create($validated);

        return response()->json([
            'message' => 'Observador creado exitosamente',
            'data' => $observador,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ubicacion_id' => 'exists:ubicacion,id',
            'tipo_observador_id' => 'exists:tipo_observador,id',
            'tipo_observador_categoria_id' => 'exists:tipo_observador_categoria,id',
            'nombre_observador' => 'string|max:255',
            'numero_documento_identidad' => 'string|max:12',
            'fecha_nacimiento' => 'date',
            'numero_celular' => 'string|max:12',
            'correo' => 'email',
            'nombre_usuario' => 'string|max:255',
            'dir_documento_identidad' => 'nullable|file|mimes:jpg,png|max:2048',
            'dir_acta_nombramiento' => 'nullable|file|mimes:jpg,png|max:2048',
        ]);
        $observador = Observador::findOrFail($id);

        //$observador->save();
        if (isset($validated['ubicacion_id'])) {
            $observador->ubicacion_id = $validated['ubicacion_id'];
        }
        if (isset($validated['tipo_observador_id'])) {
            $observador->tipo_observador_id = $validated['tipo_observador_id'];
        }
        if (isset($validated['tipo_observador_categoria_id'])) {
            $observador->tipo_observador_categoria_id = $validated['tipo_observador_categoria_id'];
        }
        if (isset($validated['nombre_observador'])) {
            $observador->nombre_observador = $validated['nombre_observador'];
        }
        if (isset($validated['numero_documento_identidad'])) {
            $observador->numero_documento_identidad = $validated['numero_documento_identidad'];
        }
        if (isset($validated['fecha_nacimiento'])) {
            $observador->fecha_nacimiento = $validated['fecha_nacimiento'];
        }
        if (isset($validated['numero_celular'])) {
            $observador->numero_celular = $validated['numero_celular'];
        }
        if (isset($validated['correo'])) {
            $observador->correo = $validated['correo'];
        }
        if (isset($validated['nombre_usuario'])) {
            $observador->nombre_usuario = $validated['nombre_usuario'];
        }
        $observador->update($validated);
        
        return response()->json([
            'message' => 'Observador actualizado exitosamente',
            'data' => $observador,
        ], 200);
    }

    public function update_with_files(Request $request, $id)
{
    // Validar los datos entrantes, incluyendo las fotos adicionales
    $validated = $request->validate([
        'ubicacion_id' => 'exists:ubicacion,id',
        'tipo_observador_id' => 'exists:tipo_observador,id',
        'tipo_observador_categoria_id' => 'exists:tipo_observador_categoria,id',
        'nombre_observador' => 'string|max:255',
        'numero_documento_identidad' => 'string|max:12',
        'fecha_nacimiento' => 'date',
        'numero_celular' => 'string|max:12',
        'correo' => 'email',
        'nombre_usuario' => 'string|max:255',
        'dir_documento_identidad' => 'nullable|file|mimes:jpg,png|max:2048',
        'dir_acta_nombramiento' => 'nullable|file|mimes:jpg,png|max:2048',
        'fotos_adicionales.*' => 'nullable|file|mimes:jpg,png|max:2048', // Validar múltiples fotos
    ]);

    // Buscar el observador por su ID
    $observador = Observador::findOrFail($id);
    $observador->fill($validated);

    // Procesar y guardar 'dir_documento_identidad' si se proporciona
    if ($request->hasFile('dir_documento_identidad')) {
        $file = $request->file('dir_documento_identidad');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/documentos'), $filename);
        $observador->dir_documento_identidad = 'uploads/documentos/' . $filename;
    }

    // Procesar y guardar 'dir_acta_nombramiento' si se proporciona
    if ($request->hasFile('dir_acta_nombramiento')) {
        $file = $request->file('dir_acta_nombramiento');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/actas'), $filename);
        $observador->dir_acta_nombramiento = 'uploads/actas/' . $filename;
    }

    $observador->save();

    return response()->json([
        'message' => 'Observador actualizado exitosamente',
        'data' => $observador,
    ], 200);
}

    public function destroy($id)
    {
        $observador = Observador::findOrFail($id);

        if ($observador->estado === 'B') {
            return response()->json([
                'message' => 'El observador ya está inactivo.'
            ], 400);
        }

        $observador->estado = 'B';
        $observador->fecha_eliminacion = now();

        $observador->save();

        return response()->json([
            'message' => 'Observador eliminado exitosamente.'
        ], 200);
    }
}
