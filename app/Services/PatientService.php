<?php
namespace App\Services;

use App\Models\Patient;
//use Illuminate\Support\Facades\Hash;
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
        // Manejo de la foto si viene
        if (isset($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {

        // Guarda en storage/app/public/pacientes
        $path = $data['foto']->store('patients', 'public');

        // Se guarda solo la ruta en la BD
        $data['foto'] = $path;
    }else{
        $data['foto'] = 'patients/default_photo.png';
    }
        return Patient::create($data);
    }



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
