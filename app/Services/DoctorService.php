<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\User;
use App\Models\ConsultingRoom;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DoctorService
{
    public function getDoctorForUser(User $user): ?Doctor
    {
        return $user->doctor;
    }

    public function ensureDoctorForUser(User $user): Doctor
    {
        return Doctor::firstOrCreate(
            ['user_id' => $user->id],
            [
                'status' => 'active',
            ]
        );
    }

    /**
     * Wizard: Update doctor profile + ensure primary consulting room + mark first_login=false
     */
    public function updateDoctorProfile(User $user, array $data): Doctor
    {
        return DB::transaction(function () use ($user, $data) {
            // 1) Ensure doctor exists
            $doctor = $this->ensureDoctorForUser($user);

            // 2) Update user info
            $userFields = Arr::only($data, ['name', 'email']);
            if (!empty($userFields)) {
                $user->fill($userFields);
            }

            // ✅ Wizard completed
            $user->first_login = false;
            $user->save();

            // 3) Update doctor info
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

            // 4) Ensure primary consulting room exists + attach as primary
            $consultorioName = $data['consultorio_name'] ?? null;

            if ($consultorioName) {
                // Check if doctor already has a primary consulting room
                $primaryRoom = $doctor->consultingRooms()
                    ->wherePivot('is_primary', true)
                    ->first();

                if (!$primaryRoom) {
                    // Create a new consulting room and attach as primary
                    $room = ConsultingRoom::create([
                        'name'   => $consultorioName,
                        'status' => 'active',
                        // Other fields are optional; keep MVP
                    ]);

                    $doctor->consultingRooms()->attach($room->id, [
                        'is_primary' => true,
                    ]);
                } else {
                    // Optional behavior:
                    // If you want to UPDATE the primary room name on wizard completion:
                    $primaryRoom->name = $consultorioName;
                    $primaryRoom->save();
                }
            }

            return $doctor->fresh(['user', 'consultingRooms']);
        });
    }

    public function getAuthenticatedDoctorProfile(User $user): ?Doctor
    {
        $doctor = $this->getDoctorForUser($user);

        if (!$doctor) {
            return null;
        }

        return $doctor->load(['user', 'consultingRooms']);
    }
}
