<?php

namespace App\Services;

use App\Models\Nurse;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NurseService
{
    /**
     * Create a new nurse (including underlying user).
     *
     * @param array $data Validated nurse data.
     */
    public function createNurse(array $data): Nurse
    {
        // 1) Create user for the nurse
        $user = new User();

        $user->name = $data['first_name'] . ' ' . $data['last_name'];
        $user->email = $data['email'] ?? null;

        // Generate a random password for now.
        $user->password = Hash::make(Str::random(16));
        $user->first_login = true;
        $user->theme = 'light';

        $user->save();

        // 2) Create Nurse record linked to this user
        $nurseData = Arr::only($data, [
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

        $nurseData['user_id'] = $user->id;

        $nurse = Nurse::create($nurseData);

        // Eager load user for convenience
        return $nurse->fresh(['user']);
    }

    /**
     * Update an existing nurse, including basic user data if provided.
     */
    public function updateNurse(Nurse $nurse, array $data): Nurse
    {
        $user = $nurse->user;

        if ($user) {
            // Update user.name if first_name or last_name change
            if (isset($data['first_name']) || isset($data['last_name'])) {
                $firstName = $data['first_name'] ?? $nurse->first_name;
                $lastName  = $data['last_name']  ?? $nurse->last_name;
                $user->name = trim($firstName . ' ' . $lastName);
            }

            if (isset($data['email'])) {
                $user->email = $data['email'];
            }

            $user->save();
        }

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
}
