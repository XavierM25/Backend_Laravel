<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        try {
            // Cargar usuarios con la relación 'role'
            $users = Usuario::with('role')->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron usuarios',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'users' => $users,
                'status' => 200
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los usuarios',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


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
        'nombre' => 'required|string',
        'apellido_paterno' => 'required|string',
        'apellido_materno' => 'required|string',
        'sexo' => 'required|in:M,F',
        'contraseña' => 'required|string|min:5',
        'email' => 'required|string|email|unique:usuarios,email',
        'fecha_nacimiento' => 'required|date',
        'campus' => 'nullable|string',
        'carrera' => 'nullable|string',
        'numero_telefono' => 'nullable|string',
        'imagen_perfil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        $data = [
            'status' => 400,
            'message' => 'Error en la validación de los datos',
            'errors' => $validator->errors()
        ];
        return response()->json($data, 400);
    }

    try {
         // Subir imagen de perfil a storage/images
        $imagePath = $request->file('imagen_perfil')->store('public/images');

         // Obtener el nombre del archivo y actualizar la ruta para que sea accesible
        $imagePath = str_replace('public/', 'storage/', $imagePath);

        // Generar username único llamando a un procedimiento almacenado
        $username = null;
        DB::select('CALL generar_usuario_unico(?, ?, ?, @username)', [
            $request->nombre,
            $request->apellido_paterno,
            $request->apellido_materno,
        ]);

        $usernameResult = DB::select('SELECT @username as username');
        if (!empty($usernameResult)) {
            $username = $usernameResult[0]->username;
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error al generar el nombre de usuario',
            ], 500);
        }

        // Crear el usuario
        $user = Usuario::create([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'sexo' => $request->sexo,
            'username' => $username,
            'contraseña' => Hash::make($request->contraseña),
            'email' => $request->email,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'rol_id' => 1, // ID del rol predeterminado para cliente
            'imagen_perfil' => $imagePath, // Ruta de la imagen guardada
            'campus' => $request->campus,
            'carrera' => $request->carrera,
            'numero_telefono' => $request->numero_telefono,
        ]);

        if(!$user){
            $data = [
                'message' => 'Error al crear el usuario',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'user' => $user,
            'status' => 201
        ];
        return response()->json($data, 201);

    } catch (Throwable $th) {
        // Capturar cualquier excepción lanzada durante el proceso
        return response()->json([
            'status' => false,
            'message' => 'Error al crear el usuario',
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
            // Cargar usuario con la relación 'role'
            $user = Usuario::with('role')->find($id);
    
            if(!$user){
                return response()->json([
                    'message' => 'Usuario no encontrado',
                    'status' => 404
                ], 404);
            }

            $user->imagen_perfil_url = Storage::url($user->imagen_perfil);
    
            return response()->json([
                'user' => $user,
                'status' => 200
            ], 200);
    
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el usuario',
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
    try {
        $user = Usuario::find($id);

        if(!$user){
            $data = [
                'message' => 'Usuario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string',
            'apellido_paterno' => 'sometimes|string',
            'apellido_materno' => 'sometimes|string',
            'sexo' => 'sometimes|in:M,F',
            'contraseña' => 'sometimes|string|min:5',
            'email' => 'sometimes|string|email|unique:usuarios,email,' . $id,
            'fecha_nacimiento' => 'sometimes|date',
            'campus' => 'sometimes|string',
            'carrera' => 'sometimes|string',
            'numero_telefono' => 'sometimes|string',
            'rol_id' => 'sometimes|integer|in:1,2', // 1 para usuario, 2 para administrador
            'imagen_perfil' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Validar tipo de imagen
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
            $user->nombre = $request->nombre;
        }

        if ($request->filled('apellido_paterno')) {
            $user->apellido_paterno = $request->apellido_paterno;
        }

        if ($request->filled('apellido_materno')) {
            $user->apellido_materno = $request->apellido_materno;
        }

        if ($request->filled('sexo')) {
            $user->sexo = $request->sexo;
        }

        if ($request->filled('contraseña')) {
            $user->contraseña = Hash::make($request->contraseña);
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('fecha_nacimiento')) {
            $user->fecha_nacimiento = $request->fecha_nacimiento;
        }

        if ($request->filled('rol_id')) {
            $user->rol_id = $request->rol_id;
        }

        if ($request->hasFile('imagen_perfil')) {
            // Eliminar la imagen de perfil anterior si no es la predeterminada
            if ($user->imagen_perfil && $user->imagen_perfil != 'public/images/default.jpg') {
                Storage::delete($user->imagen_perfil);
            }
            // Guardar la nueva imagen de perfil en storage/images
            $path = $request->file('imagen_perfil')->store('public/images');
            $user->imagen_perfil = str_replace('public/', 'storage/', $path); // Actualizar la ruta en la base de datos
        }

        if ($request->filled('campus')) {
            $user->campus = $request->campus;
        }

        if ($request->filled('carrera')) {
            $user->carrera = $request->carrera;
        }

        if ($request->filled('numero_telefono')) {
            $user->numero_telefono = $request->numero_telefono;
        }

        $user->save();

        $data = [
            'message' => 'Usuario actualizado correctamente',
            'user' => $user,
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
        $user = Usuario::find($id);

        if(!$user){
            $data = [
                'message' => 'Usuario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $user->delete();

        $data = [
            'message' => 'Usuario eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
