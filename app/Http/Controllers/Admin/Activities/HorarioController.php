<?php

namespace App\Http\Controllers\Admin\Activities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Activities\Horario;
use Illuminate\Support\Facades\Validator;
use Throwable;

class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $schedule = Horario::all();

            if ($schedule->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron horarios',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'schedule' => $schedule,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los horarios',
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
    public function store(Request $request, $subcategoria_id)
    {
        $validator = Validator::make($request->all(), [
            'dia' => 'required|string|max:255',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ], 400);
        }
    
        try {
            $schedule = Horario::create([
                'subcategoria_id' => $subcategoria_id,
                'dia' => $request->dia,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
            ]);
    
            return response()->json([
                'schedule' => $schedule,
                'status' => 201
            ], 201);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el horario',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Horario::find($id);

        if(!$schedule){
            $data = [
                'message' => 'Horario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'schedule' => $schedule,
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
        $schedule = Horario::find($id);

        if(!$schedule){
            $data = [
                'message' => 'Horario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'subcategoria_id' => 'required|integer|exists:subcategorias,id',
            'dia' => 'required|string|max:255',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            $data = [
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        if ($request->filled('subcategoria_id')) {
            $schedule->subcategoria_id = $request->subcategoria_id;
        }

        if ($request->filled('dia')) {
            $schedule->dia = $request->dia;
        }

        if ($request->filled('hora_inicio')) {
            $schedule->hora_inicio = $request->hora_inicio;
        }

        if ($request->filled('hora_fin')) {
            $schedule->hora_fin = $request->hora_fin;
        }

        $schedule->save();

        $data = [
            'message' => 'Horario actualizado correctamente',
            'schedule' => $schedule,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = Horario::find($id);

        if(!$schedule){
            $data = [
                'message' => 'Horario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $schedule->delete();

        $data = [
            'message' => 'Horario eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }
    
    public function getHorariosBySubcategoriaId($subcategoria_id)
    {
        try {
            $horarios = Horario::where('subcategoria_id', $subcategoria_id)->get();

            if($horarios->isEmpty()){
                $data = [
                    'message' => 'No se encontraron horarios para la subcategoría especificada.',
                    'status' => 404
                ];
                return response()->json($data, 404);
            }

            return response()->json([
                'success' => true,
                'horarios' => $horarios,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al recuperar los horarios.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
