<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(){
        try {
            // Obtiene el usuario autenticado desde Google
            $googleUser = Socialite::driver('google')->user();

            // Busca si existe un usuario con el mismo correo electrónico en la base de datos
            $user = Usuario::where('email', $googleUser->email)->first();

            if ($user) {
                // Si el usuario existe, inicia sesión
                Auth::login($user);
            } else {
                // Si el usuario no existe, crea uno nuevo
                $newUser = new Usuario();
                $newUser->nombre = $googleUser->name;
                $newUser->email = $googleUser->email;

                // Generar el username único utilizando el procedimiento almacenado
                $username = $this->generateUniqueUsername($googleUser->name, $googleUser->email);
                $newUser->username = $username;

                // Guardar el nuevo usuario
                $newUser->save();

                // Inicia sesión con el nuevo usuario creado
                Auth::login($newUser);
            }

            // Redirige a la página de perfil u otra página después de iniciar sesión
            return redirect()->to('/profile');
        } catch (\Exception $e) {
            // Maneja los errores de autenticación con Google
            return redirect()->to('/login')->with('error', 'Error al iniciar sesión con Google. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Genera un nombre de usuario único utilizando el procedimiento almacenado "generar_usuario_unico".
     *
     * @param string $fullName Nombre completo del usuario desde Google
     * @param string $email Correo electrónico del usuario desde Google
     * @return string Nombre de usuario único generado
     */
    private function generateUniqueUsername($fullName, $email)
    {
        $username = null;
        DB::select('CALL generar_usuario_unico(?, ?, @username)', [
            $fullName,
            explode('@', $email)[0], // Utiliza la parte antes del '@' del correo como parte del procedimiento almacenado
        ]);
        $usernameResult = DB::select('SELECT @username as username');
        if (!empty($usernameResult)) {
            $username = $usernameResult[0]->username;
        }
        return $username;
    }
}