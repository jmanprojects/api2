<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreConsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'nurse_id',
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
    ];

    protected $casts = [
        'height'            => 'float',
        'weight'            => 'float',
        'temperature'       => 'float',
        'blood_glucose'     => 'float',
        'pain_scale'        => 'integer',
        'blood_pressure_systolic'  => 'integer',
        'blood_pressure_diastolic' => 'integer',
        'heart_rate'        => 'integer',
        'respiratory_rate'  => 'integer',
        'oxygen_saturation' => 'integer',
    ];

    /**
     * Relationship: preconsultation belongs to an appointment.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Relationship: preconsultation belongs to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relationship: preconsultation belongs to a doctor.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Relationship: preconsultation may belong to a nurse (null if done by doctor).
     */
    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }
}
