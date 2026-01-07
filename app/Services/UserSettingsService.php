<?php

namespace App\Services;

use App\Models\User;

class UserSettingsService
{
    /**
     * Update user settings stored directly in the users table.
     *
     * Why a Service?
     * - SRP: Controllers should not contain business rules.
     * - Scalability: Tomorrow you can add language/timezone, etc. here.
     * - Security: We enforce a whitelist of allowed keys.
     *
     * @param User  $user Authenticated user
     * @param array $data Validated settings payload
     *
     * @return User Fresh user instance after update
     */
    public function updateSettings(User $user, array $data): User
    {
        /**
         * Whitelist update pattern:
         * We ONLY change known/allowed settings.
         * This avoids accidental updates when the payload grows.
         */
        if (array_key_exists('theme', $data)) {
            $user->theme = $data['theme'];
        }

        /**
         * Keep the write minimal and explicit.
         * If later you add more settings, extend this service safely.
         */
        $user->save();

        // Return a fresh instance to ensure the API responds with updated values.
        return $user->fresh();
    }
}
