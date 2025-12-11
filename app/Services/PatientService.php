<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatientService
{
    /**
     * Create a new patient (including underlying user) and
     * optionally attach the patient to a doctor.
     *
     * @param array       $data   Validated patient data.
     * @param Doctor|null $doctor Optional doctor to attach the patient to.
     */
    public function createPatient(array $data, ?Doctor $doctor = null): Patient
    {
        // 1) Create user for the patient
        // We generate a random password by default; you can change this behavior later
        // if you want to invite the patient with a custom flow.
        $user = new User();

        $user->name = $data['first_name'] . ' ' . $data['last_name'];
        $user->email = $data['email'] ?? ($data['alternate_email'] ?? null);

        // If no email is provided, we can leave it null or handle separately.
        // Password is randomly generated; patient can later change it via invitation flow.
        $user->password = Hash::make(Str::random(16));
        $user->first_login = true;
        $user->theme = 'light'; // default theme, can be changed later

        $user->save();

        // 2) Create Patient record linked to this user
        $patientData = Arr::only($data, [
            'first_name',
            'last_name',
            'middle_name',
            'gender',
            'birth_date',
            'marital_status',
            'occupation',
            'phone',
            'secondary_phone',
            'alternate_email',
            'document_type',
            'document_number',
            'blood_type',
            'allergies',
            'chronic_conditions',
            'emergency_contact_name',
            'emergency_contact_phone',
            'notes',
            'status',
        ]);

        $patientData['user_id'] = $user->id;

        $patient = Patient::create($patientData);

        // 3) If a doctor is provided, attach the doctor-patient relationship.
        if ($doctor) {
            $doctor->patients()->syncWithoutDetaching([
                $patient->id => [
                    'first_seen_at' => now(),
                    'last_seen_at'  => now(),
                    'status'        => 'active',
                    'notes'         => null,
                ],
            ]);
        }

        // Eager load user relation for convenience
        return $patient->fresh(['user']);
    }

    /**
     * Update an existing patient, including basic user data if provided.
     */
    public function updatePatient(Patient $patient, array $data): Patient
    {
        $user = $patient->user;

        if ($user) {
            // Update user fields if a new name or email is provided
            if (isset($data['first_name']) || isset($data['last_name'])) {
                $firstName = $data['first_name'] ?? $patient->first_name;
                $lastName  = $data['last_name']  ?? $patient->last_name;
                $user->name = trim($firstName . ' ' . $lastName);
            }

            if (isset($data['email'])) {
                $user->email = $data['email'];
            }

            $user->save();
        }

        $patientFields = Arr::only($data, [
            'first_name',
            'last_name',
            'middle_name',
            'gender',
            'birth_date',
            'marital_status',
            'occupation',
            'phone',
            'secondary_phone',
            'alternate_email',
            'document_type',
            'document_number',
            'blood_type',
            'allergies',
            'chronic_conditions',
            'emergency_contact_name',
            'emergency_contact_phone',
            'notes',
            'status',
        ]);

        $patient->fill($patientFields);
        $patient->save();

        return $patient->fresh(['user']);
    }

    /**
     * Get all patients for a given doctor.
     * This reads the many-to-many relation via doctor_patient.
     */
    public function getPatientsForDoctor(Doctor $doctor)
    {
        return $doctor->patients()
            ->with('user')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }
}
