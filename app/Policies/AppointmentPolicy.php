<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any appointments.
     *
     * IMPORTANT:
     * - This does NOT return data by itself.
     * - Your controller/service must still query scoped results (doctor/nurse/patient scope).
     * - This policy just decides if the user is allowed to access the "appointments module".
     */
    public function viewAny(User $user): bool
    {
        return $this->isDoctor($user) || $this->isNurse($user) || $this->isPatient($user);
    }

    /**
     * Determine whether the user can view a specific appointment.
     *
     * Rules:
     * - Doctor can view if appointment belongs to that doctor.
     * - Nurse can view if nurse is assigned to the appointment's consulting room.
     * - Patient can view if appointment belongs to that patient.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Patient can view only their own appointment
        if ($this->isPatient($user)) {
            return $this->patientId($user) === (int) $appointment->patient_id;
        }

        // Doctor can view only their own appointment
        if ($this->isDoctor($user)) {
            return $this->doctorId($user) === (int) $appointment->doctor_id;
        }

        // Nurse can view only appointments in rooms where nurse is assigned
        if ($this->isNurse($user)) {
            return $this->nurseAssignedToConsultingRoom($user, (int) $appointment->consulting_room_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create appointments.
     *
     * MVP rules:
     * - Doctor can create.
     * - Nurse can create (as receptionist) but only for rooms where assigned (enforced in service/controller).
     * - Patient booking is optional later; keep it false for now unless you implement it.
     */
    public function create(User $user): bool
    {
        return $this->isDoctor($user) || $this->isNurse($user);
    }

    /**
     * Determine whether the user can update an appointment.
     *
     * Update includes status changes, rescheduling, etc.
     * Rules:
     * - Doctor can update if appointment belongs to them.
     * - Nurse can update if assigned to that consulting room.
     * - Patient usually cannot update (you can add "cancel own appointment" later).
     */
    public function update(User $user, Appointment $appointment): bool
    {
        if ($this->isDoctor($user)) {
            return $this->doctorId($user) === (int) $appointment->doctor_id;
        }

        if ($this->isNurse($user)) {
            return $this->nurseAssignedToConsultingRoom($user, (int) $appointment->consulting_room_id);
        }

        return false;
    }

    /**
     * Determine whether the user can delete an appointment.
     *
     * MVP approach:
     * - Usually we do NOT delete appointments; we cancel them.
     * - Keep it strict: only doctor can delete (if you ever enable it).
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $this->isDoctor($user) && $this->doctorId($user) === (int) $appointment->doctor_id;
    }

    // ---------------------------------------------------------------------
    // Helpers (keep policy readable + avoid repeating relationship checks)
    // ---------------------------------------------------------------------

    /**
     * Determine if user has doctor profile.
     */
    protected function isDoctor(User $user): bool
    {
        return $user->relationLoaded('doctor')
            ? (bool) $user->doctor
            : $user->doctor()->exists();
    }

    /**
     * Determine if user has nurse profile.
     */
    protected function isNurse(User $user): bool
    {
        return $user->relationLoaded('nurse')
            ? (bool) $user->nurse
            : $user->nurse()->exists();
    }

    /**
     * Determine if user has patient profile.
     */
    protected function isPatient(User $user): bool
    {
        return $user->relationLoaded('patient')
            ? (bool) $user->patient
            : $user->patient()->exists();
    }

    /**
     * Get doctor_id from user profile safely.
     */
    protected function doctorId(User $user): ?int
    {
        $doctor = $user->relationLoaded('doctor') ? $user->doctor : $user->doctor()->first();
        return $doctor?->id;
    }

    /**
     * Get patient_id from user profile safely.
     */
    protected function patientId(User $user): ?int
    {
        $patient = $user->relationLoaded('patient') ? $user->patient : $user->patient()->first();
        return $patient?->id;
    }

    /**
     * Check if the nurse is assigned to a given consulting room.
     *
     * This assumes you have a pivot like:
     * consulting_room_nurse (consulting_room_id, nurse_id)
     *
     * and the Nurse model has:
     *   consultingRooms() belongsToMany(...)
     */
    protected function nurseAssignedToConsultingRoom(User $user, int $consultingRoomId): bool
    {
        $nurse = $user->relationLoaded('nurse') ? $user->nurse : $user->nurse()->first();

        if (!$nurse) {
            return false;
        }

        // Avoid loading all rooms; just check existence.
        return $nurse->consultingRooms()
            ->where('consulting_rooms.id', $consultingRoomId)
            ->exists();
    }
}
