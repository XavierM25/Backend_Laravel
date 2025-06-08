<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        try{
            $validateUser = Validator::make($request->all(),
            [
                'nombre' => 'required|string',
                'apellido_paterno' => 'required|string',
                'apellido_materno' => 'required|string',
                'sexo' => 'required|in:M,F',
                'contraseña' => 'required|string|confirmed',
                'email' => 'required|string|email|unique:usuarios,email',
                'fecha_nacimiento' => 'required|date',
                'campus' => 'nullable|string',
                'carrera' => 'nullable|string',
                'numero_telefono' => 'nullable|string',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status'=> false,
                    'message' => 'Validation error',
                    'errors' => $validateUser ->errors()
                ],401);
            }

            $username = null;
            DB::select('CALL generar_usuario_unico(?, ?, ?, @username)', [
                $request->nombre,
                $request->apellido_paterno,
                $request->apellido_materno,
            ]);
            $usernameResult = DB::select('SELECT @username as username');
            if (!empty($usernameResult)) {
                $username = $usernameResult[0]->username;
            }
            $user = new Usuario([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'sexo' => $request->sexo,
                'username' => $username,
                'contraseña' => Hash::make($request->contraseña),
                'email' => $request->email,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'rol_id' => 1, // ID del rol predeterminado para cliente
                'imagen_perfil' => 'storage/images/default.jpg',
                'campus' => $request->campus,
                'carrera' => $request->carrera,
                'numero_telefono' => $request->numero_telefono,
            ]);
            $user->save();
            $username = $user->username;
            return response()->json([
                'status'=> true,
                'message' => 'User created successfully',
                'username' => $username,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ],200);
        }catch(Throwable $th){
            return response()->json([
                'status'=> false,
                'message' => $th->getMessage(),
            ],500);
        }
    }

    // Login de usuario
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required|string',
                'contraseña' => 'required',
            ]);


            if ($validateUser->fails()) {
                return response()->json([
                    'status'=> false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            $user = Usuario::where('username', $request->username)->first();

            if (!$user || !Hash::check($request->contraseña, $user->contraseña)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario & Contraseña no coincide con nuestro registro.',
                ], 401);
            }

            // Verificar que el usuario tenga un rol asignado
            if (is_null($user->rol_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario no tiene un rol asignado.',
                ], 500);
            }

            $token = $user->createToken("API TOKEN")->plainTextToken;

            // Define la ruta home según el rol
            $home_route = $user->rol_id == 1 ? 'https://ucvdeporte3.rf.gd/Cliente/index.php' : 'https://ucvdeporte3.rf.gd/Admin/index.php';

            return response()->json([
                'status'=> true,
                'message' => 'User Logged In Successfully',
                'token' => $token,
                'rol_id' => $user->rol_id, // Incluir el rol del usuario en la respuesta
                'home_route' => $home_route, // Incluir la ruta de redirección en la respuesta
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    //DATOS DE
    public function profile(){
        $userData = auth()->user();
        return response()->json([
            'status'=> true,
            'message' => 'Profile Information',
            'data' => $userData,
            'id' => auth()->user()->id
        ],200);
    }

    public function update(Request $request, string $id)
{
    try {
        $user = Usuario::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
                'status' => 404
            ], 404);
        }

        // Validar los datos para actualizar
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string',
            'apellido_paterno' => 'sometimes|string',
            'apellido_materno' => 'sometimes|string',
            'username' => 'sometimes|string|unique:usuarios,username,' . $id,
            'contraseña' => 'sometimes|string|min:5|confirmed',
            'email' => 'sometimes|string|email|unique:usuarios,email,' . $id,
            'fecha_nacimiento' => 'sometimes|date',
            'imagen_perfil' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Validar tipo de imagen
            'campus' => 'sometimes|string',
            'carrera' => 'sometimes|string',
            'numero_telefono' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors()
            ], 400);
        }

        // Actualizar campos
        $user->nombre = $request->input('nombre', $user->nombre);
        $user->apellido_paterno = $request->input('apellido_paterno', $user->apellido_paterno);
        $user->apellido_materno = $request->input('apellido_materno', $user->apellido_materno);
        $user->username = $request->input('username', $user->username);
        $user->email = $request->input('email', $user->email);
        $user->fecha_nacimiento = $request->input('fecha_nacimiento', $user->fecha_nacimiento);
        $user->campus = $request->input('campus', $user->campus);
        $user->carrera = $request->input('carrera', $user->carrera);
        $user->numero_telefono = $request->input('numero_telefono', $user->numero_telefono);

        // Actualizar contraseña si se proporciona
        if ($request->has('contraseña')) {
            $user->contraseña = Hash::make($request->input('contraseña'));
        }

        // Actualizar imagen de perfil si se proporciona
        if ($request->hasFile('imagen_perfil')) {
            // Eliminar la imagen de perfil anterior si no es la predeterminada
            if ($user->imagen_perfil && $user->imagen_perfil != 'storage/images/default.jpg') {
                Storage::delete($user->imagen_perfil);
            }
            // Guardar la nueva imagen de perfil
            $path = $request->file('imagen_perfil')->store('public/images');
            $user->imagen_perfil = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user,
            'status' => 200
        ], 200);
    } catch (Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => 'Error al actualizar el usuario',
            'error' => $th->getMessage(),
        ], 500);
    }
}

    //Cerrar sesión
    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'status'=> true,
            'message' => 'User logged out',
            'data' => [],
        ],200);
    }



    // Recuperar contraseña
    public function recoverPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|exists:usuarios,email',
            ]);

            $user = Usuario::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Correo no encontrado'], 404);
            }
            

            $token = Str::random(60);
            $otp = rand(100000, 999999);

            PasswordReset::create([
                'usuario_id' => $user->id,
                'token' => Hash::make($token),
                'verification_code' => $otp,
            ]);

            // Envía correo electrónico con el nombre del usuario
            Mail::send('emails.recover_password', ['otp' => $otp, 'nombreUsuario' => $user->nombre], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Recuperar Contraseña');
            });

            return response()->json([
                'status' => true,
                'message' => 'Código OTP enviado con éxito a tu correo',
                'token' => $token,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }


// Validar OTP
public function validateOtp(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar el registro de PasswordReset
        $reset = PasswordReset::where('verification_code', $request->otp)->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return response()->json(['message' => 'Código OTP inválido'], 401);
        }

        // Retorna el token para su uso en la siguiente etapa
        return response()->json([
            'message' => 'Código OTP válido',
            'token' => $request->token,
        ]);

    } catch (Throwable $th) {
        return response()->json(['message' => $th->getMessage()], 500);
    }
}


// Resetear contraseña
public function resetPassword(Request $request)
{
    try {
        $request->validate([
            'token' => 'required|string',
            'otp' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $reset = PasswordReset::where('verification_code', $request->otp)->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid OTP'], 401);
        }

        $user = Usuario::find($reset->usuario_id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Validar que la nueva contraseña sea diferente a la actual
        if (Hash::check($request->password, $user->contraseña)) {
            return response()->json(['message' => 'La nueva contraseña debe ser diferente a la actual'], 400);
        }

        $user->contraseña = Hash::make($request->password);
        $user->save();

        // Eliminar el registro de PasswordReset
        $reset->delete();

        return response()->json(['message' => 'Contraseña restablecida exitosamente']);
    } catch (Throwable $th) {
        return response()->json(['message' => $th->getMessage()], 500);
    }
}



}