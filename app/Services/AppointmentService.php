<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorTimeOff;
use App\Models\DoctorWorkingHour;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    /**
     * Create a new appointment if the requested slot is available.
     *
     * This method checks:
     *  - doctor working hours for that day
     *  - overlapping appointments
     *  - doctor time off
     *
     * If the slot is not available, it throws an exception.
     */
    public function createAppointment(array $data, Doctor $doctor, Patient $patient, int $consultingRoomId, int $createdByUserId): Appointment
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $duration    = $data['duration_minutes'] ?? 20;

        // Wrap everything in a transaction to keep data consistent
        return DB::transaction(function () use ($data, $doctor, $patient, $consultingRoomId, $createdByUserId, $scheduledAt, $duration) {
            // 1) Validate that the slot is within the doctor's working hours
            if (!$this->isWithinWorkingHours($doctor, $consultingRoomId, $scheduledAt, $duration)) {
                throw new \RuntimeException('The requested time is outside doctor working hours.');
            }

            // 2) Validate that there are no overlapping appointments
            if ($this->hasOverlappingAppointment($doctor, $consultingRoomId, $scheduledAt, $duration)) {
                throw new \RuntimeException('The requested time is already booked.');
            }

            // 3) Validate that there is no time off for this doctor at that time
            if ($this->isDuringTimeOff($doctor, $consultingRoomId, $scheduledAt, $duration)) {
                throw new \RuntimeException('The doctor is unavailable at the requested time.');
            }

            // 4) Create the appointment
            $appointment = Appointment::create([
                'doctor_id'          => $doctor->id,
                'patient_id'         => $patient->id,
                'consulting_room_id' => $consultingRoomId,
                'scheduled_at'       => $scheduledAt,
                'duration_minutes'   => $duration,
                'type'               => $data['type']   ?? 'new',
                'source'             => $data['source'] ?? 'manual',
                'status'             => 'scheduled',
                'created_by_user_id' => $createdByUserId,
                'reason'             => $data['reason'] ?? null,
            ]);

            // 5) Create initial status history entry
            $this->logStatusChange($appointment, null, 'scheduled', $createdByUserId, 'Appointment created');

            return $appointment->fresh(['doctor', 'patient', 'consultingRoom']);
        });
    }

    /**
     * Check if the requested slot is inside doctor working hours.
     */
    protected function isWithinWorkingHours(Doctor $doctor, int $consultingRoomId, Carbon $scheduledAt, int $durationMinutes): bool
    {
        $weekday = (int) $scheduledAt->isoWeekday(); // 1 (Mon) - 7 (Sun)
        $start   = $scheduledAt->copy();
        $end     = $scheduledAt->copy()->addMinutes($durationMinutes);

        $workingHours = DoctorWorkingHour::where('doctor_id', $doctor->id)
            ->where('consulting_room_id', $consultingRoomId)
            ->where('weekday', $weekday)
            ->get();

        if ($workingHours->isEmpty()) {
            return false;
        }

        foreach ($workingHours as $slot) {
            $slotStart = Carbon::parse($slot->start_time)->setDateFrom($scheduledAt);
            $slotEnd   = Carbon::parse($slot->end_time)->setDateFrom($scheduledAt);

            if ($start->greaterThanOrEqualTo($slotStart) && $end->lessThanOrEqualTo($slotEnd)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if there is any overlapping appointment for that doctor and room.
     */
    protected function hasOverlappingAppointment(Doctor $doctor, int $consultingRoomId, Carbon $scheduledAt, int $durationMinutes): bool
    {
        $start = $scheduledAt->copy();
        $end   = $scheduledAt->copy()->addMinutes($durationMinutes);

        return Appointment::where('doctor_id', $doctor->id)
            ->where('consulting_room_id', $consultingRoomId)
            ->whereIn('status', [
                'scheduled',
                'confirmed',
                'in_preconsultation',
                'in_consultation',
            ])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('scheduled_at', [$start, $end->copy()->subSecond()])
                      ->orWhereRaw('? BETWEEN scheduled_at AND DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE)', [$start])
                      ->orWhereRaw('? BETWEEN scheduled_at AND DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE)', [$end]);
            })
            ->exists();
    }

    /**
     * Check if the requested slot is during a time off period.
     */
    protected function isDuringTimeOff(Doctor $doctor, int $consultingRoomId, Carbon $scheduledAt, int $durationMinutes): bool
    {
        $start = $scheduledAt->copy();
        $end   = $scheduledAt->copy()->addMinutes($durationMinutes);

        return DoctorTimeOff::where('doctor_id', $doctor->id)
            ->where(function ($query) use ($consultingRoomId) {
                $query->whereNull('consulting_room_id')
                      ->orWhere('consulting_room_id', $consultingRoomId);
            })
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_datetime', [$start, $end])
                      ->orWhereBetween('end_datetime', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_datetime', '<=', $start)
                            ->where('end_datetime', '>=', $end);
                      });
            })
            ->exists();
    }

    /**
     * Change status of an appointment and log the transition.
     */
    public function changeStatus(Appointment $appointment, string $newStatus, int $changedByUserId, ?string $reason = null): Appointment
    {
        $oldStatus = $appointment->status;

        if ($oldStatus === $newStatus) {
            return $appointment;
        }

        return DB::transaction(function () use ($appointment, $oldStatus, $newStatus, $changedByUserId, $reason) {
            $appointment->status = $newStatus;

            if ($newStatus === 'cancelled') {
                $appointment->cancelled_by_user_id = $changedByUserId;
                $appointment->cancelled_reason     = $reason;
            }

            $appointment->save();

            $this->logStatusChange($appointment, $oldStatus, $newStatus, $changedByUserId, $reason);

            return $appointment->fresh();
        });
    }

    /**
     * Log a status change into appointment_status_histories.
     */
    protected function logStatusChange(Appointment $appointment, ?string $fromStatus, string $toStatus, int $changedByUserId, ?string $reason = null): void
    {
        $appointment->statusHistory()->create([
            'from_status'       => $fromStatus,
            'to_status'         => $toStatus,
            'changed_by_user_id'=> $changedByUserId,
            'reason'            => $reason,
            'changed_at'        => now(),
        ]);
    }

    /**
     * Get appointments for a doctor in a given date range.
     */
    public function getAppointmentsForDoctor(Doctor $doctor, Carbon $from, Carbon $to)
    {
        return Appointment::with(['patient.user', 'consultingRoom'])
            ->where('doctor_id', $doctor->id)
            ->whereBetween('scheduled_at', [$from, $to])
            ->orderBy('scheduled_at')
            ->get();
    }
}
