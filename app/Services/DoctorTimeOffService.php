<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\DoctorTimeOff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DoctorTimeOffService
{
    /**
     * Create a time off record for a doctor.
     */
    public function create(Doctor $doctor, array $data): DoctorTimeOff
    {
        return DB::transaction(function () use ($doctor, $data) {
            $start = Carbon::parse($data['start_datetime']);
            $end   = Carbon::parse($data['end_datetime']);

            if ($end->lessThanOrEqualTo($start)) {
                throw new \RuntimeException('end_datetime must be after start_datetime.');
            }

            // Prevent overlapping time-off records for the same scope (optional but professional).
            if ($this->overlapsExisting($doctor, $data['consulting_room_id'] ?? null, $start, $end)) {
                throw new \RuntimeException('This time off overlaps an existing time off record.');
            }

            return DoctorTimeOff::create([
                'doctor_id'          => $doctor->id,
                'consulting_room_id' => $data['consulting_room_id'] ?? null,
                'start_datetime'     => $start,
                'end_datetime'       => $end,
                'reason'             => $data['reason'] ?? null,
            ]);
        });
    }

    /**
     * Update a time off record.
     */
    public function update(DoctorTimeOff $timeOff, array $data): DoctorTimeOff
    {
        return DB::transaction(function () use ($timeOff, $data) {
            $timeOff->fill($data);

            $start = Carbon::parse($timeOff->start_datetime);
            $end   = Carbon::parse($timeOff->end_datetime);

            if ($end->lessThanOrEqualTo($start)) {
                throw new \RuntimeException('end_datetime must be after start_datetime.');
            }

            // Overlap check (excluding itself)
            if ($this->overlapsExisting($timeOff->doctor, $timeOff->consulting_room_id, $start, $end, $timeOff->id)) {
                throw new \RuntimeException('This time off overlaps an existing time off record.');
            }

            $timeOff->save();

            return $timeOff->fresh();
        });
    }

    public function delete(DoctorTimeOff $timeOff): void
    {
        $timeOff->delete();
    }

    /**
     * List time offs for doctor, optionally filtered by consulting room.
     */
    public function list(Doctor $doctor, ?int $consultingRoomId = null)
    {
        $query = DoctorTimeOff::where('doctor_id', $doctor->id)
            ->orderBy('start_datetime');

        if ($consultingRoomId) {
            $query->where(function ($q) use ($consultingRoomId) {
                $q->whereNull('consulting_room_id')
                  ->orWhere('consulting_room_id', $consultingRoomId);
            });
        }

        return $query->get();
    }

    /**
     * Check overlapping time off for a doctor (optionally by room scope).
     */
    protected function overlapsExisting(Doctor $doctor, ?int $consultingRoomId, Carbon $start, Carbon $end, ?int $ignoreId = null): bool
    {
        $query = DoctorTimeOff::where('doctor_id', $doctor->id);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        // Scope logic:
        // - if new record is for ALL rooms (null), it overlaps with any record for any room.
        // - if new record is for a specific room, it overlaps with:
        //      a) records for that room
        //      b) records for ALL rooms (null)
        if (is_null($consultingRoomId)) {
            // All rooms
        } else {
            $query->where(function ($q) use ($consultingRoomId) {
                $q->whereNull('consulting_room_id')
                  ->orWhere('consulting_room_id', $consultingRoomId);
            });
        }

        return $query->where(function ($q) use ($start, $end) {
            // Any overlap
            $q->whereBetween('start_datetime', [$start, $end])
              ->orWhereBetween('end_datetime', [$start, $end])
              ->orWhere(function ($qq) use ($start, $end) {
                  $qq->where('start_datetime', '<=', $start)
                     ->where('end_datetime', '>=', $end);
              });
        })->exists();
    }
}
