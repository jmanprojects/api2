<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\AuthService;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        // la validación ya fue hecha por LoginRequest
        $data = $request->validated();

        // delegamos al servicio la lógica real
        $result = $this->authService->login($data);

        // devolvemos el usuario + token (Laravel lo serializa a JSON)
        return response()->json($result);
    }

    public function logout(Request $request)
    {   
        
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Logout ok']);
    }

   // Perfil de usuario autenticado
   public function profile(Request $request)
   {
       $user = $this->authService->profile($request->user());
       return response()->json($user);
   }


    public function register(RegisterRequest $request)
    {
        $data = $request->validated(); // datos ya validados
        $result = $this->authService->register($data);

        return response()->json($result, 201);
    }

   

    // Registro
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|unique:users',
    //         'password' => 'required|string|min:6',
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'Bearer',
    //     ]);
    // }

    // Login
    // public function login(Request $request)
    // {
    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'Credenciales inválidas'], 401);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'Bearer',
    //     ]);
    // }

    // Perfil de usuario autenticado
    // public function profile(Request $request)
    // {
    //     return response()->json($request->user());
    // }

    // Logout
    // public function logout(Request $request)
    // {
    //     $request->user()->tokens()->delete();

    //     return response()->json(['message' => 'Sesión cerrada']);
    // }
}
