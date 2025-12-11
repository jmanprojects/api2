<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\PreConsultation;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PreConsultationService
{
    /**
     * Create or update a preconsultation for the given appointment.
     *
     * If a preconsultation already exists, we update it.
     * Otherwise, we create a new one.
     *
     * Also updates appointment status to "in_preconsultation".
     */
    public function savePreConsultation(Appointment $appointment, array $data, User $performedBy): PreConsultation
    {
        return DB::transaction(function () use ($appointment, $data, $performedBy) {
            $doctor  = $appointment->doctor;
            $patient = $appointment->patient;

            // Determine nurse_id: if the user has a nurse profile, use it;
            // otherwise, we consider that the doctor is performing the triage.
            $nurseId = optional($performedBy->nurse)->id;

            $payload = Arr::only($data, [
                'height',
                'weight',
                'temperature',
                'blood_pressure_systolic',
                'blood_pressure_diastolic',
                'heart_rate',
                'respiratory_rate',
                'oxygen_saturation',
                'blood_glucose',
                'pain_scale',
                'notes',
            ]);

            $payload['appointment_id'] = $appointment->id;
            $payload['patient_id']     = $patient->id;
            $payload['doctor_id']      = $doctor->id;
            $payload['nurse_id']       = $nurseId;

            // If there is already a preconsultation, update it, otherwise create new
            $preConsultation = $appointment->preConsultation;

            if ($preConsultation) {
                $preConsultation->fill($payload);
                $preConsultation->save();
            } else {
                $preConsultation = PreConsultation::create($payload);
            }

            // Update appointment status if needed
            if ($appointment->status !== 'in_preconsultation') {
                $appointment->status = 'in_preconsultation';
                $appointment->save();

                // If you want to log status changes here, you could
                // inject AppointmentService and call changeStatus instead.
            }

            return $preConsultation->fresh();
        });
    }
}
