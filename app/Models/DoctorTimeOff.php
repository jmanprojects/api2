<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorTimeOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'consulting_room_id',
        'start_datetime',
        'end_datetime',
        'reason',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    /**
     * Relationship: the doctor this time off belongs to.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Relationship: the consulting room (if any) this time off applies to.
     * If null, the time off applies to all rooms for this doctor.
     */
    public function consultingRoom()
    {
        return $this->belongsTo(ConsultingRoom::class);
    }
}
