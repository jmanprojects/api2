<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AppointmentQueryService
{
    /**
     * Build a scoped query for listing appointments based on the authenticated user.
     *
     * Why a dedicated query service?
     * - Keeps controllers thin (SRP).
     * - Centralizes multi-tenant scoping logic.
     * - Reusable for exports, calendars, dashboards, etc.
     */
    public function forUser(User $user): Builder
    {
        $query = Appointment::query()
            // Always eager load what the frontend needs
            ->with([
                'patient.user',
                'doctor.user',
                'consultingRoom',
            ])
            ->orderBy('scheduled_at');

        /**
         * Doctor scope:
         * - Only appointments that belong to the authenticated doctor.
         */
        if ($this->isDoctor($user)) {
            return $query->where('doctor_id', $this->doctorId($user));
        }

        /**
         * Nurse scope:
         * - Only appointments in consulting rooms where the nurse is assigned.
         * - Prevents nurses from seeing other clinics/rooms.
         */
        if ($this->isNurse($user)) {
            $roomIds = $this->nurseConsultingRoomIds($user);

            // If nurse has no rooms assigned, return empty set safely.
            return $query->whereIn('consulting_room_id', $roomIds);
        }

        /**
         * Patient scope:
         * - Only their own appointments.
         * - Read-only access enforced elsewhere (policies/routes).
         */
        if ($this->isPatient($user)) {
            return $query->where('patient_id', $this->patientId($user));
        }

        /**
         * Fallback (should not happen if auth is correct):
         * - Return no records.
         */
        return $query->whereRaw('1 = 0');
    }

    // ---------------------------------------------------------------------
    // Helpers (same pattern you used in Policy: consistent + readable)
    // ---------------------------------------------------------------------

    protected function isDoctor(User $user): bool
    {
        return $user->relationLoaded('doctor')
            ? (bool) $user->doctor
            : $user->doctor()->exists();
    }

    protected function isNurse(User $user): bool
    {
        return $user->relationLoaded('nurse')
            ? (bool) $user->nurse
            : $user->nurse()->exists();
    }

    protected function isPatient(User $user): bool
    {
        return $user->relationLoaded('patient')
            ? (bool) $user->patient
            : $user->patient()->exists();
    }

    protected function doctorId(User $user): ?int
    {
        $doctor = $user->relationLoaded('doctor') ? $user->doctor : $user->doctor()->first();
        return $doctor?->id;
    }

    protected function patientId(User $user): ?int
    {
        $patient = $user->relationLoaded('patient') ? $user->patient : $user->patient()->first();
        return $patient?->id;
    }

    /**
     * Return consulting room IDs assigned to the nurse.
     *
     * Assumes:
     * - Nurse model has consultingRooms() belongsToMany(...)
     */
    protected function nurseConsultingRoomIds(User $user): array
    {
        $nurse = $user->relationLoaded('nurse') ? $user->nurse : $user->nurse()->first();

        if (!$nurse) {
            return [];
        }

        return $nurse->consultingRooms()
            ->pluck('consulting_rooms.id')
            ->toArray();
    }
}
