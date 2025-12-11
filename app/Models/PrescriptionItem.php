<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medicine_name',
        'dose',
        'frequency',
        'duration',
        'route',
        'instructions',
    ];

    /**
     * Relationship: prescription item belongs to a prescription.
     */
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
