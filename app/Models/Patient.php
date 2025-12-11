<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'birth_date',
        'marital_status',
        'occupation',
        'phone',
        'secondary_phone',
        'alternate_email',
        'document_type',
        'document_number',
        'blood_type',
        'allergies',
        'chronic_conditions',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Relationship: a Patient belongs to one User.
     * 1:1 link to base user information (login, email, etc.).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: a patient can be treated by many doctors.
     * Many-to-many via the doctor_patient pivot table.
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_patient')
            ->withTimestamps()
            ->withPivot('first_seen_at', 'last_seen_at', 'status', 'notes');
    }

    /**
     * Placeholder for appointments relation.
     * A patient can have many appointments.
     */
    // public function appointments()
    // {
    //     return $this->hasMany(Appointment::class);
    // }


        /**
     * A patient can have many appointments.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

        /**
     * A patient can have many prescriptions.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }


}
