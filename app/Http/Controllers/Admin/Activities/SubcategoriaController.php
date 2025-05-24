<?php

namespace App\Http\Controllers\Admin\Activities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Activities\Subcategoria;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SubcategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $subcategorie = Subcategoria::all();

            if ($subcategorie->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron subcategorias',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'schedule' => $subcategorie,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las subcategorias',
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
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'vacantes' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $data = [
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $subcategorie = Subcategoria::create([
            'nombre' => $request->nombre,
            'categoria_id' => $request->categoria_id,
            'vacantes' => $request->vacantes,
        ]);


        if(!$subcategorie){
            $data = [
                'message' => 'Error al crear la subcategoria',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'subcategorie' => $subcategorie,
            'status' => 201
        ];
        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subcategorie = Subcategoria::find($id);

        if(!$subcategorie){
            $data = [
                'message' => 'Subcategoria no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'subcategorie' => $subcategorie,
            'status' => 200
        ];

        return response()->json($data, 200);
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
    try {
        $subcategoria = Subcategoria::find($id);

        if (!$subcategoria) {
            $data = [
                'message' => 'Subcategoría no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'vacantes' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $data = [
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        // Actualizar los campos solo si se proporcionan en la solicitud
        if ($request->filled('nombre')) {
            $subcategoria->nombre = $request->nombre;
        }

        if ($request->filled('vacantes')) {
            $subcategoria->vacantes = $request->vacantes;
        }

        $subcategoria->save();

        // Recuperar nuevamente la subcategoría actualizada después de guardar
        $subcategoria = Subcategoria::find($id);

        $data = [
            'message' => 'Subcategoría actualizada correctamente',
            'subcategorie' => $subcategoria, // Devolver la subcategoría actualizada
            'status' => 200
        ];

        return response()->json($data, 200);
    } catch (Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => 'Error al actualizar la subcategoría',
            'error' => $th->getMessage(),
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subcategorie = Subcategoria::find($id);

        if(!$subcategorie){
            $data = [
                'message' => 'Subcategoria no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $subcategorie->delete();

        $data = [
            'message' => 'Subcategoria eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function getVacantes($subcategoria_id)
    {
        try {
            $subcategoria = Subcategoria::findOrFail($subcategoria_id);
            
            // Calcular vacantes disponibles restando las vacantes ocupadas del total de vacantes
            $vacantesDisponibles = $subcategoria->vacantes - $subcategoria->vacantes_ocupadas;
    
            return response()->json([
                'totalVacantes' => $subcategoria->vacantes,
                'vacantesOcupadas' => $subcategoria->vacantes_ocupadas,
                'vacantesDisponibles' => $vacantesDisponibles,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las vacantes',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

}
