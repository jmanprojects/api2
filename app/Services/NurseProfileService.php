<?php

namespace App\Services;

use App\Models\Nurse;
use App\Models\User;
use Illuminate\Support\Arr;

class NurseProfileService
{
    /**
     * Get nurse profile for user (if exists).
     */
    public function getNurseForUser(User $user): ?Nurse
    {
        return $user->nurse;
    }

    /**
     * Ensure user has a Nurse profile.
     * Creates it with defaults if not exists.
     */
    public function ensureNurseForUser(User $user): Nurse
    {
        return Nurse::firstOrCreate(
            ['user_id' => $user->id],
            [
                'status' => 'active',
                // Puedes setear defaults aquí si quieres
            ]
        );
    }

    /**
     * Update authenticated nurse profile (and basic user fields if needed).
     */
    public function updateNurseProfile(User $user, array $data): Nurse
    {
        // 1) Ensure profile exists
        $nurse = $this->ensureNurseForUser($user);

        // 2) Update basic user fields (if provided)
        $userFields = Arr::only($data, ['email']);
        if (!empty($userFields)) {
            $user->fill($userFields);
        }

        // Actualiza user.name para mantener coherencia
        if (isset($data['first_name']) || isset($data['last_name'])) {
            $first = $data['first_name'] ?? $nurse->first_name;
            $last  = $data['last_name'] ?? $nurse->last_name;
            $user->name = trim($first . ' ' . $last);
        }

        // Si tu app usa first_login como “pendiente onboarding”, aquí podrías apagarlo:
        // $user->first_login = false;

        $user->save();

        // 3) Update nurse fields
        $nurseFields = Arr::only($data, [
            'first_name',
            'last_name',
            'middle_name',
            'position',
            'phone',
            'secondary_phone',
            'license_number',
            'notes',
            'status',
        ]);

        $nurse->fill($nurseFields);
        $nurse->save();

        return $nurse->fresh(['user']);
    }

    /**
     * Fetch authenticated nurse profile with user relation.
     */
    public function getAuthenticatedNurseProfile(User $user): ?Nurse
    {
        $nurse = $this->getNurseForUser($user);

        if (!$nurse) {
            return null;
        }

        return $nurse->load('user');
    }
}
