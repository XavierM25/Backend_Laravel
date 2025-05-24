<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Video;
use App\Models\Admin\Like;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Throwable;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $videos = Video::with('categoria', 'subcategoria')->get();

            return response()->json([
                'videos' => $videos,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los videos',
                'error' => $th->getMessage(),
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
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'campus' => 'required|string|max:255',
                'categoria_id' => 'required|exists:categorias,id',
                'subcategoria_id' => 'required|exists:subcategorias,id',
                'visibilidad' => 'required|in:publico,privado',
                'archivo_video' => 'required|file|mimes:mp4,mov,ogg,qt|max:200000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Error en la validación de los datos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $archivoVideo = $request->file('archivo_video')->store('public/videos');
            $archivoVideo = str_replace('public/', 'storage/', $archivoVideo);

            $video = Video::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'campus' => $request->campus,
                'categoria_id' => $request->categoria_id,
                'subcategoria_id' => $request->subcategoria_id,
                'visibilidad' => $request->visibilidad,
                'archivo_video' => $archivoVideo,
                'likes' => 0,
            ]);

            return response()->json([
                'video' => $video,
                'status' => 201
            ], 201);
        } catch (Throwable $th) {
            Log::error('Error al crear el video: ' . $th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error al crear el video',
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
            $video = Video::with('categoria', 'subcategoria')->findOrFail($id);

            return response()->json([
                'video' => $video,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Video no encontrado',
                'error' => $th->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'status' => 404,
                'message' => 'Video no encontrado',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string',
            'campus' => 'sometimes|required|string|max:255',
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'subcategoria_id' => 'sometimes|required|exists:subcategorias,id',
            'visibilidad' => 'sometimes|required|in:publico,privado',
            'archivo_video' => 'sometimes|file|mimes:mp4,mov,ogg,qt|max:200000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            if ($request->hasFile('archivo_video')) {
                // Eliminar el archivo de video anterior
                if ($video->archivo_video) {
                    Storage::delete($video->archivo_video);
                }

                $archivoVideo = $request->file('archivo_video')->store('public/videos');
                $video->archivo_video = str_replace('public/', 'storage/', $archivoVideo);
            }

            $video->update($request->only([
                'titulo', 'descripcion', 'campus', 'categoria_id', 'subcategoria_id', 'visibilidad'
            ]));

            return response()->json([
                'video' => $video,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el video',
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
            $video = Video::findOrFail($id);

            // Eliminar el archivo de video
            if ($video->archivo_video) {
                // Convertir la ruta 'storage/videos/...' a 'public/videos/...'
                $archivoPath = str_replace('storage/', 'public/', $video->archivo_video);
                Storage::delete($archivoPath);
            }

            $video->delete();

            return response()->json([
                'message' => 'Video eliminado',
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            Log::error('Error al eliminar el video: ' . $th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el video',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Like a specific video.
     */
    public function like(string $id)
    {
        try {
            // Obtener el usuario autenticado
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autenticado',
                ], 401);
            }

            // Verificar si el usuario ya dio like al video
            $likeExists = DB::table('likes')->where('usuario_id', $user->id)->where('video_id', $id)->exists();

            if ($likeExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ya diste like a este video',
                ], 400);
            }

            // Crear un nuevo like
            DB::table('likes')->insert([
                'usuario_id' => $user->id,
                'video_id' => $id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Incrementar el contador de likes en el video
            Video::where('id', $id)->increment('likes');

            // Recargar el video con los likes actualizados
            $video = Video::findOrFail($id);

            return response()->json([
                'video' => $video,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al dar like al video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Unlike a specific video.
     */
    public function unlike(string $id)
    {
        try {
            // Obtener el usuario autenticado
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autenticado',
                ], 401);
            }

            // Verificar si el usuario dio like al video
            $like = DB::table('likes')->where('usuario_id', $user->id)->where('video_id', $id)->first();

            if (!$like) {
                return response()->json([
                    'status' => false,
                    'message' => 'No diste like a este video',
                ], 400);
            }

            // Eliminar el like
            DB::table('likes')->where('id', $like->id)->delete();

            // Decrementar el contador de likes en el video
            Video::where('id', $id)->decrement('likes');

            // Recargar el video con los likes actualizados
            $video = Video::findOrFail($id);

            return response()->json([
                'video' => $video,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al quitar like al video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
