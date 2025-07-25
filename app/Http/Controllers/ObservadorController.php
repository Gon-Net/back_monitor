<?php

namespace App\Http\Controllers;

use App\Models\Observador;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use Illuminate\Validation\ValidationException;
class ObservadorController extends Controller
{
    public function getWithValues(Request $request)
    {
        $ubicacion_id = $request->input('ubicacion_id');
        $tipo_observador_id = $request->input('tipo_observador_id');
        $tipo_usuarioapk_id = $request->input('tipo_usuarioapk_id');
        $perPage = $request->input('items', 100);
        $observadoresFiltrados = ApiHelper::getAlloweds(Observador::class, all: true)->pluck('id');
        $observadores = Observador::whereIn('id', $observadoresFiltrados);

        if ($ubicacion_id !== null){
            $observadores = $observadores->where('ubicacion_id', $ubicacion_id);
        }
        if ($tipo_observador_id !== null){
            $observadores = $observadores->where('tipo_observador_id', $tipo_observador_id);
        }
        if ($tipo_usuarioapk_id !== null){
            $observadores = $observadores->where('tipo_usuarioapk_id', $tipo_usuarioapk_id);
        }

        $observadoresConRelaciones = $observadores
            ->with(['ubicacion', 'tipoObservador', 'tipoObservadorCategoria'])
            ->paginate($perPage);
        return response()->json($observadoresConRelaciones, 200);
    }
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(Observador::class, $perPage), 200);
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ubicacion_id' => 'required|exists:ubicacion,id',
                'tipo_observador_id' => 'required|exists:tipo_observador,id',
                'tipo_usuarioapk_id' => 'required|exists:tipo_observador_categoria,id',
                'nombre_observador' => 'required|string|max:255',
                'numero_documento_identidad' => 'required|string|max:12',
                'fecha_nacimiento' => 'required|date',
                'numero_celular' => 'required|string|max:12',
                'correo' => 'required|email',
                'nombre_usuario' => 'required|string|max:255',
                'dir_documento_identidad' => 'nullable|file|mimes:jpg,png|max:2048', 
                'dir_acta_nombramiento' => 'nullable|file|mimes:jpg,png|max:2048',
                'comunidad_aledania' => 'nullable|string|max:100'
            ]);

            /*
            $duplicateCI = Observador::where('numero_documento_identidad', $request->get('numero_documento_identidad'))->count(); 

            if ($duplicateCI > 0)
            {
                return response()->json([
                    'message' => 'El CI es duplicado, ingrese otro.',
                ], 404); 
            }
            */

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
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al crear.',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    public function update($id, Request $request)
    {
        try{
            $validated = $request->validate([
                'ubicacion_id' => 'exists:ubicacion,id',
                'tipo_observador_id' => 'exists:tipo_observador,id',
                'tipo_usuarioapk_id' => 'exists:tipo_observador_categoria,id',
                'nombre_observador' => 'string|max:255',
                'numero_documento_identidad' => 'string|max:12',
                'fecha_nacimiento' => 'date',
                'numero_celular' => 'string|max:12',
                'correo' => 'email',
                'nombre_usuario' => 'string|max:255',
                'dir_documento_identidad' => 'nullable|file|mimes:jpg,png|max:2048',
                'dir_acta_nombramiento' => 'nullable|file|mimes:jpg,png|max:2048',
                'comunidad_aledania' => 'nullable|string|max:100'
            ]);
            
            $observador = Observador::findOrFail($id);

            /*
            $duplicateCI = Observador::where('numero_documento_identidad', $request->get('numero_documento_identidad'))->count(); 
            
            //If numero_documento_identidad is duplicated and it is different of the current observador apply also when the numero_documento_identidad is edit
            if (($duplicateCI >= 1 && 
                $id == $observador->id &&
                $request->get('numero_documento_identidad') != $observador->numero_documento_identidad 
                ))
            {
                return response()->json([
                    'message' => 'El CI es duplicado, ingrese otro.',
                ], 404); 
            }
            */

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
            if (isset($validated['comunidad_aledania'])) {
                $observador->comunidad_aledania = $validated['comunidad_aledania'];
            }
            $observador->update($validated);
            
            return response()->json([
                'message' => 'Observador actualizado exitosamente',
                'data' => $observador,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al actualizar.',
                'errors' => $e->errors(),
            ], 404);
        }
    }
    public function findObservador(Request $request)
    {
        $name = $request->get('nombre_usuario');
        $ndi = $request->get('numero_documento_identidad');

        try{
            $observador = Observador::where('nombre_usuario', $name)
                ->where('numero_documento_identidad', $ndi)
                ->first();
            if ($observador === null){
                return response()->json([
                    'message' => 'Observador no encontrado'
                ], 404);
            }
            if ($observador->estado === 'B') {
                return response()->json([
                    'message' => 'Ubicacion no disponible'
                ], 404);
            }
        return response()->json([
            'id' => $observador->id,
            'ubicacion_id' => $observador->ubicacion_id,
            'tipo_observador_id' => $observador->tipo_observador_id,
            'tipo_usuarioapk_id' => $observador->tipo_usuarioapk_id,
            'nombre_usuario' => $observador->nombre_usuario,
            'numero_documento_identidad'=> $observador->numero_documento_identidad
        ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al encontrar',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    public function update_with_files(Request $request, $id)
    {
        try{
            // Validar los datos entrantes, incluyendo las fotos adicionales
            $validated = $request->validate([
                'ubicacion_id' => 'exists:ubicacion,id',
                'tipo_observador_id' => 'exists:tipo_observador,id',
                'tipo_usuarioapk_id' => 'exists:tipo_observador_categoria,id',
                'nombre_observador' => 'string|max:255',
                'numero_documento_identidad' => 'string|max:12',
                'fecha_nacimiento' => 'date',
                'numero_celular' => 'string|max:12',
                'correo' => 'email',
                'nombre_usuario' => 'string|max:255',
                'dir_documento_identidad' => 'nullable|file|mimes:jpg,png|max:2048',
                'dir_acta_nombramiento' => 'nullable|file|mimes:jpg,png|max:2048',
                'fotos_adicionales.*' => 'nullable|file|mimes:jpg,png|max:2048', // Validar múltiples fotos
                'comunidad_aledania' => 'nullable|string|max:100'
            ]);

            // Buscar el observador por su ID
            $observador = Observador::findOrFail($id);
            $observador->fill($validated);
            
            /*
            $duplicateCI = Observador::where('numero_documento_identidad', $request->get('numero_documento_identidad'))->count(); 

            //If numero_documento_identidad is duplicated and it is different of the current observador apply also when the numero_documento_identidad is edit
            if (($duplicateCI >= 1 && 
                $id == $observador->id &&
                $request->get('numero_documento_identidad') != $observador->numero_documento_identidad 
                ))
            {
                return response()->json([
                    'message' => 'El CI es duplicado, ingrese otro.',
                ], 404); 
            }
            */

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
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error al actualizar.',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
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
        } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Error al eliminar.',
            'errors' => $e->errors(),
        ], 404);
    }
        
    }
}
