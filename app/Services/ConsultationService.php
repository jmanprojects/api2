<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ConsultationService
{
    protected AppointmentService $appointmentService;

    /**
     * Inject AppointmentService to reuse status change logging.
     */
    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Start a consultation for a given appointment.
     * If a consultation already exists, we just update started_at if it is null.
     */
    public function startConsultation(Appointment $appointment, User $doctorUser): Consultation
    {
        $doctor = $appointment->doctor;

        // Optional: ensure that the authenticated user is the doctor of this appointment.
        if ($doctorUser->id !== $doctor->user_id) {
            // You can throw an exception or handle authorization elsewhere
        }

        return DB::transaction(function () use ($appointment, $doctorUser) {
            $consultation = $appointment->consultation;

            if (!$consultation) {
                $consultation = Consultation::create([
                    'appointment_id'        => $appointment->id,
                    'patient_id'            => $appointment->patient_id,
                    'doctor_id'             => $appointment->doctor_id,
                    'pre_consultation_id'   => optional($appointment->preConsultation)->id,
                    'started_at'            => Carbon::now(),
                ]);
            } elseif (!$consultation->started_at) {
                $consultation->started_at = Carbon::now();
                $consultation->save();
            }

            // Update appointment status to "in_consultation" with history
            $this->appointmentService->changeStatus($appointment, 'in_consultation', $doctorUser->id);

            return $consultation->fresh();
        });
    }

    /**
     * Save consultation clinical data (SOAP-like fields).
     */
    public function saveConsultationData(Consultation $consultation, array $data): Consultation
    {
        $fields = Arr::only($data, [
            'chief_complaint',
            'subjective',
            'objective',
            'assessment',
            'plan',
            'notes',
        ]);

        $consultation->fill($fields);
        $consultation->save();

        return $consultation->fresh();
    }

    /**
     * Finish consultation: set ended_at and mark appointment as completed.
     */
    public function finishConsultation(Consultation $consultation, User $doctorUser): Consultation
    {
        return DB::transaction(function () use ($consultation, $doctorUser) {
            if (!$consultation->started_at) {
                $consultation->started_at = Carbon::now();
            }

            $consultation->ended_at = Carbon::now();
            $consultation->save();

            $appointment = $consultation->appointment;

            // Mark appointment as completed
            $this->appointmentService->changeStatus($appointment, 'completed', $doctorUser->id);

            // You might also want to update doctor_patient.last_seen_at here.

            return $consultation->fresh();
        });
    }
}
