<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


class UserService
{
   /**
     * Cambio de contraseña en flujo normal (NO primer login).
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'currentPassword' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }

    /**
     * Establecer contraseña en primer login (no requiere contraseña actual).
     */
    public function setPasswordFirstLogin(User $user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->first_login = false; // o 0, según tengas el tipo en la DB
        $user->save();
    }
}
