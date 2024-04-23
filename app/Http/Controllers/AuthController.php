<?php

namespace App\Http\Controllers;

use Auth; // Librerí de autenticación
use Carbon\Carbon; // Librería para manejo de fechas
use Illuminate\Http\Request;

// Importando los modelos
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Adjunto los datos del usuario
        $data = array(
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        );

        // Guardamos el usuario a la base de datos
        $newUser = new User($data);

        // Validamos si la persistencia fue exitosa
        if ($newUser->save() == false) {
            // Respuesta en caso de error
            return response()->json([
                'message' => 'No se pudo registrar el usuario',
                'data' => NULL,
            ], 500);
        }

        // Respuesta en caso de éxito
        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'data' => $newUser,
        ], 201);
    }

    public function login(Request $request)
    {
        // Obtenemos el usuario según correo electrónico
        $currentUser = User::where('email', $request->email)->first();

        // Obtenemos las credenciales del usuario
        $credentials = array(
            'email' => $request->email,
            'password' => $request->password
        );

        // Verificamos las credencias y el usuario
        if (
            Auth::attempt($credentials) == false
            ||
            $currentUser == NULL
        ) {
            // Respuesta en caso de error
            return response()->json([
                'message' => 'Credenciales incorrectas',
                'data' => NULL,
            ], 401);
        }

        // Obtengo el usuario que acaba de iniciar sesión
        $user = $request->user();

        // Asiganamos nombre al token
        $tokenResult = $user->createToken('User Access Token');

        // Obtenemos el token
        $token = $tokenResult->token;

        // Definimos la duración del token (3 horas)
        $token->expires_at = Carbon::now()->addHours(3);

        // Guardamos el token
        $token->save();

        // Respuesta en caso de éxito
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'user' => $user,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($token->expires_at)
                                    ->toDateTimeString(),
            ],
        ], 200);
    }

    public function profile(Request $request) 
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Respuesta en caso de éxito
        return response()->json([
            'message' => 'Perfil del usuario',
            'data' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        // Obtenemos el usuario autenticado
        $user = $request->user();

        // Revocamos (eliminamos) el token
        $user->token()->revoke();

        // Respuesta en caso de éxito
        return response()->json([
            'message' => 'Cierre de sesión exitoso',
            'data' => NULL,
        ], 200);
    }
}
