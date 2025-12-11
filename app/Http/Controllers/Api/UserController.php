<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\UserService;



class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // Si firts_login es true/1 → está en primer login
        $isFirstLogin = (bool) $user->first_login;

        if ($isFirstLogin) {
            //  Primer login: solo pedimos la nueva contraseña
            $data = $request->validate([
                'newPassword' => ['required', 'string', 'min:6'],
            ]);

            $this->userService->setPasswordFirstLogin(
                $user,
                $data['newPassword']
            );
        } else {
            //  Cambio normal: contraseña actual + nueva
            $data = $request->validate([
                'currentPassword' => ['required'],
                'newPassword'     => ['required', 'string', 'min:6'],
            ]);

            $this->userService->changePassword(
                $user,
                $data['currentPassword'],
                $data['newPassword']
            );
        }

        return response()->json([
            'message' => 'Contraseña actualizada correctamente',
        ]);
    }
}
