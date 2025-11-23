<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'name',
        'last_name',
        'address',
        'phone',
        'degree',
        'speciality',
        'professional_license',
        'photo',
        'clinic_name',
        'clinic_logo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
