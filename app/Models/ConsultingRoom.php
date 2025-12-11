<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Nurse;


class ConsultingRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'clinic_id',
        'name',
        'code',
        'description',
        'floor',
        'room_number',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'country',
        'phone',
        'status',
    ];

    /**
     * A consulting room optionally belongs to a clinic.
     * If clinic_id is null, it is considered an independent/private office.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * A consulting room can be used by many doctors.
     * This is defined through the consulting_room_doctor pivot table.
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'consulting_room_doctor')
            ->withTimestamps()
            ->withPivot('is_primary');
    }

    /**
     * Placeholder for nurses relation.
     * We will implement this later when the Nurse model/table is created.
     */
    // public function nurses()
    // {
    //     return $this->belongsToMany(Nurse::class, 'consulting_room_nurse')
    //         ->withTimestamps()
    //         ->withPivot('role_in_room', 'is_primary');
    // }

    /**
     * A consulting room can have many nurses.
     * Many-to-many via consulting_room_nurse pivot table.
     */
    public function nurses()
    {
        return $this->belongsToMany(Nurse::class, 'consulting_room_nurse')
            ->withTimestamps()
            ->withPivot('role_in_room', 'is_primary');
    }

}
