<?php
// app/Http/Controllers/Api/PatientController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
         $this->patientService = $patientService;
        
    }

    public function index(Request $request)
    {
        $search = $request->query('search'); // ← aquí se obtiene ?search=algo
        $patient = $this->patientService->index($search);

        if ($patient->isEmpty()) {
            return response()->json([
                'message' => 'No se encontró ningún paciente'
            ], 404);
        }

        return response()->json($patient);
    }


    public function store(Request $request)
{
    $data = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email' => 'nullable|email|unique:patients,email',
        'telefono' => 'nullable|string|max:20',
        'fecha_nacimiento' => 'nullable|date',
        'sexo' => 'nullable|string|in:M,F',
        'direccion' => 'nullable|string',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $patient = $this->patientService->create($data);
    return response()->json($patient, 201);
}
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'nombre' => 'required|string|max:255',
    //         'apellido' => 'required|string|max:255',
    //         'email' => 'nullable|email|unique:patients',
    //         'telefono' => 'nullable|string|max:20',
    //         'fecha_nacimiento' => 'nullable|date',
    //         'sexo' => 'nullable|string|in:M,F',
    //         'direccion' => 'nullable|string',
    //         'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     $patient = $this->patientService->create($data);
    //     return response()->json($patient, 201);
    // }

    public function show($id)
    {
        return response()->json($this->patientService->getById($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|unique:patients,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|string|in:M,F',
            'direccion' => 'nullable|string',
        ]);

        $patient = $this->patientService->update($id, $data);
        return response()->json($patient);
    }

    public function uploadFoto(Request $request, $id)
    {
        $request->validate([
            'foto' => 'required|image|max:2048'
        ]);

        return $this->patientService->subirFoto($id, $request->file('foto'));
    }


    public function destroy($id)
    {
        $this->patientService->delete($id);
        return response()->json(null, 204);
    }

    // public function searchPatient($search){
    //     $pacientes = $this->patientService->index($search);

    //     if ($pacientes->isEmpty()) {
    //         return response()->json([
    //             'message' => 'No se encontró ningún paciente'
    //         ], 404);
    //     }
    //     return response()->json($pacientes);
    // }
}
