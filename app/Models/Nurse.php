<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
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
        'position',
        'phone',
        'secondary_phone',
        'license_number',
        'notes',
        'status',
    ];

    /**
     * Relationship: a Nurse belongs to one User.
     * 1:1 link to base user information (login, email, etc.).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A nurse can work in multiple consulting rooms.
     * Many-to-many via consulting_room_nurse pivot table.
     */
    public function consultingRooms()
    {
        return $this->belongsToMany(ConsultingRoom::class, 'consulting_room_nurse')
            ->withTimestamps()
            ->withPivot('role_in_room', 'is_primary');
    }
}
