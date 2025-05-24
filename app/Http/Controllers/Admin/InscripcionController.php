<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inscripcion;
use App\Models\Admin\Activities\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        try {
            $inscripciones = Inscripcion::with(['usuario', 'categoria', 'subcategoria', 'horario'])->get();

            if ($inscripciones->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron inscripciones',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'inscripciones' => $inscripciones,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las inscripciones',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos en la solicitud
        $validator = Validator::make($request->all(), [
            'categoria_id' => 'required|exists:categorias,id',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'horario_id' => 'required|exists:horarios,id',
            'comentarios' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Obtener el usuario autenticado (usando Sanctum)
            $usuario = auth()->user();

            if (!$usuario) {
                return response()->json([
                    'status' => false,
                    'message' => 'No hay usuario autenticado',
                    'error' => 'Usuario no autenticado',
                ], 401);
            }

            // Verificar si el usuario ya tiene una inscripción en la misma subcategoría
            $existingInscripcion = Inscripcion::where('usuario_id', $usuario->id)
                ->where('subcategoria_id', $request->subcategoria_id)
                ->exists();

            if ($existingInscripcion) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ya tienes una inscripción en esta subcategoría',
                    'error' => 'Inscripción duplicada',
                ], 400);
            }

            // Verificar si hay vacantes disponibles en la subcategoría
            $subcategoria = Subcategoria::findOrFail($request->subcategoria_id);
            if ($subcategoria->vacantes - $subcategoria->vacantes_ocupadas > 0) {
                $numero_vacante = $subcategoria->vacantes_ocupadas + 1;

                // Crear la inscripción
                $inscripcion = Inscripcion::create([
                    'usuario_id' => $usuario->id,
                    'categoria_id' => $request->categoria_id,
                    'subcategoria_id' => $request->subcategoria_id,
                    'horario_id' => $request->horario_id,
                    'estado' => 'Pendiente',
                    'numero_vacante' => $numero_vacante,
                    'comentarios' => $request->comentarios,
                ]);

                // Actualizar las vacantes ocupadas en la subcategoría
                $subcategoria->vacantes_ocupadas++;
                $subcategoria->save();

                return response()->json([
                    'message' => 'Inscripción realizada con éxito',
                    'inscripcion' => $inscripcion,
                    'status' => 201
                ], 201);
            } else {
                return response()->json([
                    'message' => 'No hay vacantes disponibles',
                    'status' => 400
                ], 400);
            }
        } catch (Throwable $th) {
            // Capturar y manejar errores inesperados
            return response()->json([
                'status' => false,
                'message' => 'Error al crear la inscripción',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $inscripcion = Inscripcion::with(['usuario', 'categoria', 'subcategoria', 'horario'])->findOrFail($id);

            return response()->json([
                'inscripcion' => $inscripcion,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Inscripción no encontrada',
                'error' => $th->getMessage(),
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'sometimes|required|string|in:Aprobado,Pendiente,Rechazado',
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'subcategoria_id' => 'sometimes|required|exists:subcategorias,id',
            'horario_id' => 'sometimes|required|exists:horarios,id',
            'comentarios' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $inscripcion = Inscripcion::findOrFail($id);
            $data = $request->all();

            // Comprobar si hay cambios en la subcategoria_id
            if (isset($data['subcategoria_id']) && $data['subcategoria_id'] != $inscripcion->subcategoria_id) {
                $newSubcategoria = Subcategoria::findOrFail($data['subcategoria_id']);
                if ($newSubcategoria->vacantes - $newSubcategoria->vacantes_ocupadas > 0) {
                    // Reducir vacantes ocupadas en la antigua subcategoría
                    $oldSubcategoria = Subcategoria::findOrFail($inscripcion->subcategoria_id);
                    $oldSubcategoria->vacantes_ocupadas--;
                    $oldSubcategoria->save();

                    // Incrementar vacantes ocupadas en la nueva subcategoría
                    $newSubcategoria->vacantes_ocupadas++;
                    $newSubcategoria->save();

                    $inscripcion->numero_vacante = $newSubcategoria->vacantes_ocupadas;
                } else {
                    return response()->json([
                        'message' => 'No hay vacantes disponibles en la nueva subcategoría',
                        'status' => 400
                    ], 400);
                }
            }

            $inscripcion->fill($data);
            $inscripcion->save();

            return response()->json([
                'message' => 'Inscripción actualizada con éxito',
                'inscripcion' => $inscripcion,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la inscripción',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $inscripcion = Inscripcion::findOrFail($id);
            $subcategoria = Subcategoria::findOrFail($inscripcion->subcategoria_id);

            $subcategoria->vacantes_ocupadas--;
            $subcategoria->save();

            $inscripcion->delete();

            return response()->json([
                'message' => 'Inscripción eliminada con éxito',
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar la inscripción',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
