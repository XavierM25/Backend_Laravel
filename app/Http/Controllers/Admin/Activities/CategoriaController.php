<?php

namespace App\Http\Controllers\Admin\Activities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Activities\Subcategoria;
use App\Models\Admin\Activities\Categoria;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categorie = Categoria::all();

            if ($categorie->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron categorias',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'categories' => $categorie,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las categorias',
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
        ]);

        if ($validator->fails()) {
            $data = [
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $categorie = Categoria::create([
            'nombre' => $request->nombre,
        ]);


        if(!$categorie){
            $data = [
                'message' => 'Error al crear la categoria',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'categorie' => $categorie,
            'status' => 201
        ];
        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categorie = Categoria::find($id);

        if(!$categorie){
            $data = [
                'message' => 'Categoria no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'categorie' => $categorie,
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
        $categorie = Categoria::find($id);

        if(!$categorie){
            $data = [
                'message' => 'Categoria no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $data = [
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        if ($request->filled('nombre')) {
            $categorie->nombre = $request->nombre;
        }

        $categorie->save();

        $data = [
            'message' => 'Categoria actualizado correctamente',
            'categorie' => $categorie,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categorie = Categoria::find($id);

        if(!$categorie){
            $data = [
                'message' => 'Categoria no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $categorie->delete();

        $data = [
            'message' => 'Categoria eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function getSubcategorias($categoria_id){
        try {
            $subcategorias = Subcategoria::where('categoria_id', $categoria_id)->get();

            return response()->json([
                'subcategorias' => $subcategorias,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las subcategorías',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
