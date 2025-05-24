<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Habilidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class HabilidadController extends Controller
{
    public function index()
    {
        try {
            $habilidades = Habilidades::with(['usuario', 'categoria', 'subcategoria'])->get();

            if ($habilidades->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron habilidades',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'habilidades' => $habilidades,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las habilidades',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'subcategoria_id' => 'required|integer|exists:subcategorias,id',
            'puntos' => 'required|integer',
            'estado' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $habilidad = Habilidades::create($request->all());

            return response()->json([
                'habilidad' => $habilidad,
                'status' => 201
            ], 201);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear la habilidad',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $habilidad = Habilidades::with(['usuario', 'categoria', 'subcategoria'])->findOrFail($id);

            return response()->json([
                'habilidad' => $habilidad,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Habilidad no encontrada',
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
            'usuario_id' => 'sometimes|required|integer|exists:usuarios,id',
            'categoria_id' => 'sometimes|required|integer|exists:categorias,id',
            'subcategoria_id' => 'sometimes|required|integer|exists:subcategorias,id',
            'puntos' => 'sometimes|required|integer',
            'estado' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $habilidad = Habilidades::findOrFail($id);

            // Actualización parcial
            if ($request->filled('usuario_id')) {
                $habilidad->usuario_id = $request->usuario_id;
            }
            if ($request->filled('categoria_id')) {
                $habilidad->categoria_id = $request->categoria_id;
            }
            if ($request->filled('subcategoria_id')) {
                $habilidad->subcategoria_id = $request->subcategoria_id;
            }
            if ($request->filled('puntos')) {
                $habilidad->puntos = $request->puntos;
            }
            if ($request->filled('estado')) {
                $habilidad->estado = $request->estado;
            }

            $habilidad->save();

            return response()->json([
                'habilidad' => $habilidad,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la habilidad',
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
            $habilidad = Habilidades::findOrFail($id);
            $habilidad->delete();

            return response()->json([
                'message' => 'Habilidad eliminada',
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar la habilidad',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getUserHabilities($id)
    {
        try {
            // Buscar las habilidades del usuario por su ID
            $habilidades = Habilidades::where('usuario_id', $id)
                ->with(['categoria', 'subcategoria'])
                ->get();

            if ($habilidades->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron habilidades para el usuario',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'habilidades' => $habilidades,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las habilidades del usuario',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getUserHabilitiesWithPosition($id)
    {
        try {
            // Obtener todas las habilidades del usuario
            $habilidades = Habilidades::where('usuario_id', $id)
                ->with(['subcategoria'])
                ->get();

            // Verificar si se encontraron habilidades para el usuario
            if ($habilidades->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron habilidades para el usuario con ID ' . $id,
                    'status' => 404
                ], 404);
            }

            // Obtener la subcategoría del usuario (asumiendo que la subcategoría es la misma para todas las habilidades)
            $subcategoriaId = $habilidades->first()->subcategoria->id;

            // Obtener la posición general del usuario basado en los puntos acumulados en todas las habilidades
            $posicionGeneral = Habilidades::where('puntos', '>', function ($query) use ($id) {
                    // Subquery para obtener los puntos del usuario
                    $query->select('puntos')
                        ->from('habilidades')
                        ->where('usuario_id', $id);
                })
                ->count() + 1;

            // Obtener la posición del usuario dentro de su subcategoría basado en los puntos acumulados
            $posicionSubcategoria = Habilidades::whereHas('subcategoria', function ($query) use ($subcategoriaId) {
                    $query->where('id', $subcategoriaId);
                })
                ->where('puntos', '>', function ($query) use ($id) {
                    // Subquery para obtener los puntos del usuario
                    $query->select('puntos')
                        ->from('habilidades')
                        ->where('usuario_id', $id);
                })
                ->count() + 1;

            // Retornar la respuesta JSON con las habilidades, posición general y posición en la subcategoría
            return response()->json([
                'habilidades' => $habilidades,
                'posicion_general' => $posicionGeneral,
                'posicion_subcategoria' => $posicionSubcategoria,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las habilidades del usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
