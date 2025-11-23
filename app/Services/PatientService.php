<?php
namespace App\Services;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
//use Illuminate\Validation\ValidationException;

class PatientService
{
    public function getAll()
    {
        return Patient::all();
    }

    public function getById($id)
    {
        return Patient::findOrFail($id);
    }


    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            // 1️ Crear usuario con rol paciente
            $user = User::create([
                'name'     => $data['nombre'],   
                'email'    => $data['email'] ?? null,
                'password' => Hash::make(str()->random(16)),  // clave temporal
                'rol'      => 'paciente',
            ]);

            // 2️ Manejar la foto si viene
            $fotoPath = null;
            if (isset($data['foto'])) {
                $fotoPath = $data['foto']->store('patients', 'public');
            }else{
                     $fotoPath= $data['foto'] = 'patients/default_photo.png';
                }

            // 3️ Crear registro paciente usando user_id
            $patient = Patient::create([
                'user_id'         => $user->id,
                'nombre'          => $data['nombre'],
                'apellido'        => $data['apellido'],
                'email'           => $data['email'],
                'telefono'        => $data['telefono'] ?? null,
                'fecha_nacimiento'=> $data['fecha_nacimiento'] ?? null,
                'sexo'            => $data['sexo'] ?? null,
                'direccion'       => $data['direccion'] ?? null,
                'foto'            => $fotoPath,
            ]);

            return [
                'user'     => $user,
                'patient'  => $patient,
            ];
        });
    }

    // public function create(array $data)
    // {
    //     // Manejo de la foto si viene
    //     if (isset($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {

    //     // Guarda en storage/app/public/pacientes
    //     $path = $data['foto']->store('patients', 'public');

    //     // Se guarda solo la ruta en la BD
    //     $data['foto'] = $path;
    // }else{
    //     $data['foto'] = 'patients/default_photo.png';
    // }
    //     return Patient::create($data);
    // }



    public function update($id, array $data)
    {
        $patient = Patient::findOrFail($id);
        $patient->update($data);
        return $patient;
    }

    public function subirFoto($id, $photo)
    {   

        if (isset($photo) && $photo instanceof \Illuminate\Http\UploadedFile) {
         
            $patient = Patient::findOrFail($id);


            // Guarda en storage/app/public/pacientes
            $path = $photo->store('patients', 'public');
    
            // Se guarda solo la ruta en la BD
            $patient->foto = $path;
            $patient->save();
        }
        
        // $patient = Patient::findOrFail($id);

        // $nombreArchivo = 'paciente_'.$id.'_'.time().'.'.$archivo->getClientOriginalExtension();

        // Guarda en /storage/app/public/pacientes
        // $archivo->storeAs('patients/', $nombreArchivo);
        // $path = $data['foto']->store('patients', 'public');

        // Actualiza el registro
        // $patient->foto = $nombreArchivo;
        // $patient->save();

        return [
            'message' => 'Foto subida correctamente',
            'foto' => $path,
            'paciente' => $patient->id,
        ];
    }


    public function delete($id)
    {
        $patient = Patient::findOrFail($id);
        return $patient->delete();
    }

    public function index($search = null)
    {
        if ($search && trim($search) !== '') {
            $searchTerm = strtolower(trim($search));
    
            $patient = Patient::whereRaw("LOWER(CONCAT(nombre, ' ', apellido)) LIKE ?", ["%{$searchTerm}%"])
                ->orWhereRaw("LOWER(CONCAT(apellido, ' ', nombre)) LIKE ?", ["%{$searchTerm}%"])
                ->get();
    
            return $patient;
        }
    
        return Patient::all();
    }
    

}
