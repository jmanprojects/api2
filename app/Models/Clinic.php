<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;


class Clinic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * We explicitly list fields that can be mass assigned.
     */
    protected $fillable = [
        'name',
        'legal_name',
        'tax_id',
        'phone',
        'email',
        'website',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'country',
        'status',
    ];

    /**
     * A clinic can have many consulting rooms.
     */
    public function consultingRooms()
    {
        return $this->hasMany(ConsultingRoom::class);
    }

    /**
     * A clinic can have many doctors through consulting rooms
     * (if you want to access all doctors associated to this clinic).
     * This is optional and can be refined later.
     */
    // public function doctors()
    // {
    //     return $this->hasManyThrough(
    //         Doctor::class,
    //         ConsultingRoom::class,
    //         'clinic_id',             // Foreign key on consulting_rooms table...
    //         'id',                    // Foreign key on doctors table (used via pivot later)
    //         'id',                    // Local key on clinics table
    //         'id'                     // Local key on consulting_rooms table
    //     );
    // }

        /**
     * Get all doctors that work in this clinic through its consulting rooms.
     *
     * This is not a "pure" Eloquent relationship (you cannot eager load it
     * with ->with('doctors')), but it returns a Collection of Doctor models
     * correctly filtered by this clinic.
     *
     * Usage:
     *   $clinicDoctors = $clinic->doctors();
     */
    public function doctors()
    {
        return Doctor::whereHas('consultingRooms', function ($query) {
            $query->where('clinic_id', $this->id);
        })->get();
    }

}
