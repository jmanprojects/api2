<?php
namespace App\Services;

use App\Models\Patient;
//use Illuminate\Support\Facades\Hash;
//use Illuminate\Validation\ValidationException;

class PatientService
{
    public function getAll()
    {
        return Patient::all();
    }

    public function getById($id)
    {
        return Patient::findOrFail($id);
    }

    public function create(array $data)
    {
        return Patient::create($data);
    }

    public function update($id, array $data)
    {
        $patient = Patient::findOrFail($id);
        $patient->update($data);
        return $patient;
    }

    public function delete($id)
    {
        $patient = Patient::findOrFail($id);
        return $patient->delete();
    }
}
