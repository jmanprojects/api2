<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * We explicitly list the fields we allow to be filled.
     */
    protected $fillable = [
        'user_id',
        'professional_license',
        'specialty',
        'secondary_specialty',
        'phone',
        'gender',
        'birth_date',
        'bio',
        'photo_path',
        'status',
    ];

    /**
     * The attributes that should be cast.
     * birth_date is handled as a Carbon date instance.
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Relationship: a Doctor belongs to one User.
     * 1:1 link to the base user information (name, email, etc.).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Placeholder relationship for future consulting rooms.
     * A doctor can be linked to many consulting rooms via a pivot table.
     *
     * Example of future relation:
     * return $this->belongsToMany(ConsultingRoom::class)->withPivot(...);
     */
    // public function consultingRooms()
    // {
    //     // Will be implemented once ConsultingRoom model/table exists.
    //     return $this->belongsToMany(ConsultingRoom::class)
    //         ->withTimestamps()
    //         ->withPivot('is_primary');
    // }

    /**
 * A doctor can work in multiple consulting rooms.
 * This many-to-many relation uses the consulting_room_doctor pivot table.
 */
public function consultingRooms()
{
    return $this->belongsToMany(ConsultingRoom::class, 'consulting_room_doctor')
        ->withTimestamps()
        ->withPivot('is_primary');
}


    /**
     * Placeholder relationship for patients through doctor_patient pivot.
     * A doctor can have many patients.
     */
    // public function patients()
    // {
    //     // Will be implemented later when Patient model exists.
    //     return $this->belongsToMany(Patient::class, 'doctor_patient')
    //         ->withTimestamps()
    //         ->withPivot('first_seen_at', 'last_seen_at', 'status', 'notes');
    // }

    /**
     * A doctor can have many patients (clinical relationship).
     * Many-to-many via doctor_patient pivot table.
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'doctor_patient')
            ->withTimestamps()
            ->withPivot('first_seen_at', 'last_seen_at', 'status', 'notes');
    }


    /**
     * Placeholder relationship for appointments.
     * A doctor can have many appointments.
     */
    // public function appointments()
    // {
    //     return $this->hasMany(Appointment::class);
    // }

    /**
     * A doctor can have many appointments.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

        /**
     * A doctor can issue many prescriptions.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }


}
