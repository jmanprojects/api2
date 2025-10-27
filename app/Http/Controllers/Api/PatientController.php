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

    public function index()
    {
        return response()->json($this->patientService->getAll());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients',
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|string|in:M,F',
            'direccion' => 'nullable|string',
        ]);

        $patient = $this->patientService->create($data);
        return response()->json($patient, 201);
    }

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

    public function destroy($id)
    {
        $this->patientService->delete($id);
        return response()->json(null, 204);
    }
}
