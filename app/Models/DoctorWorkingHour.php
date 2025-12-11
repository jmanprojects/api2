<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorWorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'consulting_room_id',
        'weekday',
        'start_time',
        'end_time',
        'slot_duration_minutes',
    ];

    /**
     * Relationship: the doctor this schedule belongs to.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Relationship: the consulting room where these hours apply.
     */
    public function consultingRoom()
    {
        return $this->belongsTo(ConsultingRoom::class);
    }
}
