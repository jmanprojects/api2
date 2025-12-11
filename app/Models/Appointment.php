<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'consulting_room_id',
        'scheduled_at',
        'duration_minutes',
        'type',
        'source',
        'status',
        'created_by_user_id',
        'cancelled_by_user_id',
        'cancelled_reason',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Relationship: the doctor for this appointment.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Relationship: the patient for this appointment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relationship: the consulting room where this appointment takes place.
     */
    public function consultingRoom()
    {
        return $this->belongsTo(ConsultingRoom::class);
    }

    /**
     * Relationship: user who created this appointment.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Relationship: user who cancelled this appointment (if any).
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    /**
     * Relationship: status history entries for this appointment.
     */
    public function statusHistory()
    {
        return $this->hasMany(AppointmentStatusHistory::class);
    }

        /**
     * Relationship: one appointment may have one preconsultation record.
     */
    public function preConsultation()
    {
        return $this->hasOne(PreConsultation::class);
    }

    /**
     * Relationship: one appointment may have one main consultation.
     */
    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

}
