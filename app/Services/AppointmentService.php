<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;

class AppointmentService
{
    public function getAll()
    {
        return Appointment::with('patient', 'user')->latest()->get();
    }

    public function find($id)
    {
        return Appointment::with('patient', 'user')->findOrFail($id);
    }

    public function create(array $data, $user)
    {
        $validator = Validator::make($data, [
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'reason' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'cost' => 'nullable|numeric',
            'status' => 'in:pendiente,completada,cancelada',
        ]);

        $validator->validate();

        return Appointment::create([
            ...$data,
            'user_id' => $user->id,
        ]);
    }

    public function update($id, array $data)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($data);
        return $appointment;
    }

    public function delete($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
    }
}
