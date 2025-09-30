<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * login: recibe ['email' => ..., 'password' => ...]
     * Devuelve ['user' => User, 'token' => '...'] o lanza ValidationException
     */
    public function login(array $credentials)
    {
        // 1) buscar usuario por email
        $user = User::where('email', $credentials['email'] ?? null)->first();

        // 2) si no existe o la contraseña no coincide -> error
        if (! $user || ! Hash::check($credentials['password'] ?? '', $user->password)) {
            // lanzamos una excepcion que Laravel puede convertir en response 422 con mensajes
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        // 3) crear token (Sanctum) — 'api_token' es un nombre descriptivo
        $token = $user->createToken('api_token')->plainTextToken;

        // 4) devolver datos que el controller convertirá a JSON
        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    /**
     * logout: eliminar el token actual (si se usa token en header)
     */
    public function logout($user)
    {
        // si usamos tokens personales, borramos el token activo
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        } else {
            // alternativa: borrar todos los tokens del usuario
            $user->tokens()->delete();
        }

        return ['message' => 'Sesión cerrada correctamente'];
    }

    // profile: obtener los datos del usuario auntenticado.
    public function profile($user)
    {
        return $user; // devilve todos los datos del usuario o se puede filtrar
    }

    //registrar nuevo usuario
    public function register(array $data)
    {
        // Crear el usuario
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Crear token automáticamente
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
