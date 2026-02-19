<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'fecha_nacimiento',
        'sexo',
        'direccion',
        'foto'
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }
}
