<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\DoctorWorkingHour;
use Illuminate\Support\Facades\DB;

class DoctorWorkingHourService
{
    /**
     * Create a working hour record for the given doctor.
     * We keep this in a service to enforce business rules consistently.
     */
    public function create(Doctor $doctor, array $data): DoctorWorkingHour
    {
        return DB::transaction(function () use ($doctor, $data) {

            // Basic consistency check
            if (isset($data['start_time'], $data['end_time']) && $data['end_time'] <= $data['start_time']) {
                throw new \RuntimeException('end_time must be after start_time.');
            }

            $payload = [
                'doctor_id'             => $doctor->id,
                'consulting_room_id'    => $data['consulting_room_id'],
                'weekday'               => $data['weekday'],
                'start_time'            => $data['start_time'],
                'end_time'              => $data['end_time'],
                'slot_duration_minutes' => $data['slot_duration_minutes'] ?? 20,
            ];

            return DoctorWorkingHour::create($payload);
        });
    }

    /**
     * Update an existing working hour record.
     */
    public function update(DoctorWorkingHour $workingHour, array $data): DoctorWorkingHour
    {
        return DB::transaction(function () use ($workingHour, $data) {
            $workingHour->fill($data);

            // Re-check time consistency if either time changed
            if (($workingHour->start_time && $workingHour->end_time) && ($workingHour->end_time <= $workingHour->start_time)) {
                throw new \RuntimeException('end_time must be after start_time.');
            }

            $workingHour->save();

            return $workingHour->fresh();
        });
    }

    /**
     * Delete a working hour record.
     */
    public function delete(DoctorWorkingHour $workingHour): void
    {
        $workingHour->delete();
    }

    /**
     * List doctor working hours, optionally filtered by consulting_room_id.
     */
    public function list(Doctor $doctor, ?int $consultingRoomId = null)
    {
        $query = DoctorWorkingHour::where('doctor_id', $doctor->id)
            ->orderBy('weekday')
            ->orderBy('start_time');

        if ($consultingRoomId) {
            $query->where('consulting_room_id', $consultingRoomId);
        }

        return $query->get();
    }
}
