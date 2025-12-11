<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'pre_consultation_id',
        'started_at',
        'ended_at',
        'chief_complaint',
        'subjective',
        'objective',
        'assessment',
        'plan',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    /**
     * Relationship: consultation belongs to an appointment.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Relationship: consultation belongs to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relationship: consultation belongs to a doctor.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Relationship: consultation may be linked to a preconsultation.
     */
    public function preConsultation()
    {
        return $this->belongsTo(PreConsultation::class);
    }


        /**
     * A consultation can have many prescriptions
     * (in case the doctor wants to issue multiple at different times).
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

}
