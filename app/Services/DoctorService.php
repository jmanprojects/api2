<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Arr;

class DoctorService
{
    /**
     * Get the Doctor model associated with the given user.
     * If the user is not a doctor (no record found), this returns null.
     */
    public function getDoctorForUser(User $user): ?Doctor
    {
        // We assume the relationship user->doctor is defined in User model.
        // If it's not yet, it should be added as: return $this->hasOne(Doctor::class);
        return $user->doctor;
    }

    /**
     * Ensure that the given user has an associated Doctor profile.
     * If it does not exist, we create one with default values.
     *
     * This can be useful when a doctor logs in for the first time
     * and you want to have a Doctor record ready to be filled.
     */
    public function ensureDoctorForUser(User $user): Doctor
    {
        // firstOrCreate tries to find a record; if none exists, it creates it.
        return Doctor::firstOrCreate(
            ['user_id' => $user->id],
            [
                'status' => 'active',
            ]
        );
    }

    /**
     * Update the authenticated doctor's profile.
     *
     * This method:
     *  - updates basic user fields (like name, email) if provided
     *  - updates doctor-specific fields
     *
     * @param User  $user The authenticated user (must be a doctor).
     * @param array $data Validated data from the request.
     */
    public function updateDoctorProfile(User $user, array $data): Doctor
    {
        // 1) Ensure the user has a Doctor profile
        $doctor = $this->ensureDoctorForUser($user);

        // 2) Update "User" basic info (if provided)
        $userFields = Arr::only($data, ['name', 'email']);

        if (!empty($userFields)) {
            $user->fill($userFields);
            $user->save();
        }

        // 3) Update Doctor-specific information
        $doctorFields = Arr::only($data, [
            'professional_license',
            'specialty',
            'secondary_specialty',
            'phone',
            'gender',
            'birth_date',
            'bio',
            'photo_path',
            'status',
        ]);

        $doctor->fill($doctorFields);
        $doctor->save();

        return $doctor->fresh(['user']);
    }

    /**
     * Get the authenticated doctor's profile with the linked user.
     * This is useful for "profile" endpoints like /api/doctors/me.
     */
    public function getAuthenticatedDoctorProfile(User $user): ?Doctor
    {
        $doctor = $this->getDoctorForUser($user);

        if (!$doctor) {
            return null;
        }

        // Eager load user relation to avoid N+1 issues.
        return $doctor->load('user');
    }
}
