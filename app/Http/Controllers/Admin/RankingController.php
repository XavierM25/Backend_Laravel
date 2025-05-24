<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Habilidades;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RankingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Habilidades::with(['usuario', 'subcategoria', 'subcategoria.categoria'])
                ->selectRaw('usuario_id, subcategoria_id, SUM(puntos) as total_puntos, MAX(estado) as estado')
                ->groupBy('usuario_id', 'subcategoria_id')
                ->orderBy('total_puntos', 'desc');

            if ($request->has('subcategoria_id')) {
                $query->where('subcategoria_id', $request->subcategoria_id);
            }

            $rankings = $query->get();

            if ($rankings->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron rankings para la subcategoría seleccionada',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'rankings' => $rankings,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los rankings',
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $habilidades = Habilidades::with(['usuario', 'subcategoria'])
                ->selectRaw('usuario_id, subcategoria_id, SUM(puntos) as total_puntos')
                ->groupBy('usuario_id', 'subcategoria_id')
                ->where('usuario_id', $id)
                ->orderBy('total_puntos', 'desc')
                ->get();

            if ($habilidades->isEmpty()) {
                return response()->json([
                    'message' => 'Usuario no encontrado en el ranking',
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
                'message' => 'Error al obtener el ranking del usuario',
                'error' => $th->getMessage(),
            ], 500);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $habilidades = Habilidades::where('usuario_id', $id)->get();

            if ($habilidades->isEmpty()) {
                return response()->json([
                    'message' => 'Usuario no encontrado en el ranking',
                    'status' => 404
                ], 404);
            }

            Habilidades::where('usuario_id', $id)->delete();

            return response()->json([
                'message' => 'Usuario eliminado del ranking',
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el usuario del ranking',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
