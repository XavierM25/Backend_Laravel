<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Reporte;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Usuario;
use App\Models\Admin\Habilidades;
use App\Models\Admin\Activities\Categoria;
use App\Models\Admin\Activities\Subcategoria;
use App\Models\Admin\Activities\Horario;
use App\Models\Admin\Inscripcion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $reportes = Reporte::all();
            if ($reportes->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron reportes',
                    'status' => 404
                ], 404);
            }
            return response()->json([
                'reportes' => $reportes,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los reportes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'modulo' => 'required|string|in:Usuarios,Ranking,Actividades,Inscripciones',
                'formato' => 'required|string|in:Excel,PDF',
                'fecha_inicio' => 'required|date_format:Y-m-d',
                'fecha_fin' => 'required|date_format:Y-m-d',
                'ordenar_por' => 'nullable|string|in:username,created_at,carrera,puntos,posicion,updated_at,dia,vacantes,usuario,categoria',
                'orden' => 'nullable|string|in:asc,desc',
                'filtros' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Error en la validación de los datos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $filePath = $this->generateReport($request->modulo, $request->formato, $request->all());

            $reporte = Reporte::create([
                'modulo' => $request->modulo,
                'formato' => $request->formato,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'ordenar_por' => $request->ordenar_por,
                'orden' => $request->orden,
                'filtros' => $request->filtros,
                'file_path' => $filePath,
            ]);

            return response()->json([
                'reporte' => $reporte,
                'status' => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el reporte',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function download($id)
    {
        try {
            $reporte = Reporte::findOrFail($id);

            // Obtener el nombre del archivo desde la ruta almacenada en la base de datos
            $fileName = basename($reporte->file_path);

            // Devolver la descarga del archivo con el nombre correcto
            return Storage::download($reporte->file_path, $fileName);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Reporte no encontrado',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al descargar el reporte',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $reporte = Reporte::findOrFail($id);
            Storage::delete($reporte->file_path);
            $reporte->delete();

            return response()->json([
                'message' => 'Reporte eliminado correctamente',
                'status' => 200
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Reporte no encontrado',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el reporte',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateReport($modulo, $formato, $filters)
    {
        try {
            $data = $this->fetchData($modulo, $filters);

            // Aplicar ordenamiento si se especifica
            if (isset($filters['ordenar_por'])) {
                $ordenarPor = $filters['ordenar_por'];
                $orden = isset($filters['orden']) && $filters['orden'] == 'desc' ? 'desc' : 'asc';

                $data = $data->sortBy($ordenarPor, SORT_REGULAR, $orden === 'desc');
            }

            if ($formato == 'Excel') {
                $filePath = "reportes/{$modulo}_reporte_" . time() . ".xlsx";
                Excel::store(new \App\Exports\GenericExport($data), $filePath);
            } elseif ($formato == 'PDF') {
                $view = 'reportes.' . strtolower($modulo);
                $pdf = PDF::loadView($view, ['data' => $data]);
                $filePath = "reportes/{$modulo}_reporte_" . time() . ".pdf";
                Storage::put($filePath, $pdf->output());
            }

            return $filePath;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function fetchData($modulo, $filters)
    {
        try {
            switch ($modulo) {
                case 'Usuarios':
                    return $this->fetchUsuarios($filters);
                case 'Ranking':
                    return $this->fetchRanking($filters);
                case 'Actividades':
                    return $this->fetchActividades($filters);
                case 'Inscripciones':
                    return $this->fetchInscripciones($filters);
                default:
                    return [];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function fetchUsuarios($filters)
    {
        try {
            $query = Usuario::select('id', 'nombre', 'apellido_paterno', 'apellido_materno', 'sexo', 'username', 'email', 'fecha_nacimiento', 'imagen_perfil', 'campus', 'carrera', 'numero_telefono', 'created_at');

            // Aplicar rango de fechas si se especifica fecha de inicio y fin
            if (isset($filters['fecha_inicio']) && isset($filters['fecha_fin'])) {
                $fechaInicio = $filters['fecha_inicio'];
                $fechaFin = $filters['fecha_fin'];
                $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            }

            // Aplicar ordenación si se especifica el campo y el orden
            if (isset($filters['ordenar_por']) && in_array($filters['ordenar_por'], ['username', 'created_at', 'carrera'])) {
                $ordenarPor = $filters['ordenar_por'];
                $orden = isset($filters['orden']) && $filters['orden'] == 'desc' ? 'desc' : 'asc';
                $query->orderBy($ordenarPor, $orden);
            }

            $usuarios = $query->get();

            // Verificar si no se encontraron resultados y retornar mensaje apropiado
            if ($usuarios->isEmpty()) {
                return [
                    'message' => 'No se encontraron datos en ese rango de fecha',
                ];
            }

            return $usuarios;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function fetchRanking($filters)
    {
        try {
            $query = Habilidades::query();

            // Selección de campos y suma de puntos
            $query->select('usuario_id', 'subcategoria_id', 'estado', 'updated_at');
            $query->selectRaw('SUM(puntos) as puntos');
            $query->groupBy('usuario_id', 'subcategoria_id', 'estado', 'updated_at');

            // Carga condicional de relaciones
            $query->with(['usuario', 'subcategoria']);

            // Ordenamiento
            if (isset($filters['ordenar_por'])) {
                $ordenarPor = $filters['ordenar_por'];
                $orden = isset($filters['orden']) && $filters['orden'] == 'desc' ? 'desc' : 'asc';

                switch ($ordenarPor) {
                    case 'puntos':
                        $query->orderBy('puntos', $orden);
                        break;
                    case 'posicion':
                        // Ordenar por puntos de forma predeterminada si se selecciona posición
                        $query->orderBy('puntos', $orden);
                        break;
                    case 'updated_at':
                        $query->orderBy('updated_at', $orden);
                        break;
                    default:
                        $query->orderBy('puntos', 'desc'); // Ordenar por puntos descendente por defecto
                        break;
                }
            } else {
                // Ordenar por puntos descendente por defecto si no se especifica ordenar_por
                $query->orderBy('puntos', 'desc');
            }

            // Obtener habilidades
            $habilidades = $query->get();

            // Calcular la posición basada en los puntos
            $rankings = $habilidades->map(function ($habilidad, $index) {
                $habilidad->posicion = $index + 1;
                return $habilidad;
            });

            // Verificar si no se encontraron habilidades y retornar mensaje apropiado
            if ($rankings->isEmpty()) {
                return [
                    'message' => 'No se encontraron datos en ese rango de fecha',
                ];
            }

            return $rankings;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function fetchActividades($filters)
    {
        try {
            $query = Subcategoria::query();

            // Selección de campos y relaciones
            $query->select('id', 'categoria_id', 'nombre', 'vacantes', 'created_at', 'updated_at', 'vacantes_ocupadas');
            $query->with(['categoria', 'horarios']);

            // Ordenamiento
            if (isset($filters['ordenar_por'])) {
                $ordenarPor = $filters['ordenar_por'];
                $orden = isset($filters['orden']) && $filters['orden'] == 'desc' ? 'desc' : 'asc';

                switch ($ordenarPor) {
                    case 'dia':
                        // Ordenar los horarios por 'dia' dentro de la relación 'horarios'
                        $query->with(['horarios' => function ($q) use ($orden) {
                            $q->orderBy('dia', $orden);
                        }]);
                        break;
                    case 'vacantes':
                        $query->orderBy('vacantes', $orden);
                        break;
                    default:
                        $query->orderBy($ordenarPor, $orden);
                        break;
                }
            }

            // Obtener las subcategorías
            $subcategorias = $query->get();

            return $subcategorias;

        } catch (\Exception $e) {
            throw $e;
        }
    }


    private function fetchInscripciones($filters)
    {
        try {
            $query = Inscripcion::query();

            // Ordenamiento por defecto
            if (isset($filters['ordenar_por'])) {
                switch ($filters['ordenar_por']) {
                    case 'usuario':
                        $query->orderBy('usuario_id', $filters['orden'] ?? 'asc');
                        break;
                    case 'categoria':
                        $query->orderBy('categoria_id', $filters['orden'] ?? 'asc');
                        break;
                    case 'created_at':
                        $query->orderBy('created_at', $filters['orden'] ?? 'asc');
                        break;
                    default:
                        $query->orderBy($filters['ordenar_por'], $filters['orden'] ?? 'asc');
                        break;
                }
            }

            // Obtener las inscripciones con las relaciones solicitadas
            $inscripciones = $query->with(['usuario', 'categoria', 'subcategoria', 'horario'])->get();

            return $inscripciones;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function preview(Request $request)
    {
        try {
            $filters = $request->all();
            $modulo = $filters['modulo'];

            switch ($modulo) {
                case 'Usuarios':
                    $data = $this->fetchUsuarios($filters);
                    break;
                case 'Ranking':
                    $data = $this->fetchRanking($filters);
                    break;
                case 'Actividades':
                    $data = $this->fetchActividades($filters);
                    break;
                case 'Inscripciones':
                    $data = $this->fetchInscripciones($filters);
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Módulo no válido para previsualización',
                    ], 400);
            }

            if (isset($data['message'])) {
                return response()->json([
                    'status' => 404,
                    'message' => $data['message']
                ], 404);
            }

            return response()->json([
                'data' => $data,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al previsualizar el reporte',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
