<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\DoctorService;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected PatientService $patientService;
    protected DoctorService $doctorService;

    public function __construct(PatientService $patientService, DoctorService $doctorService)
    {
        $this->patientService = $patientService;
        $this->doctorService = $doctorService;

        // Example middlewares:
        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor'); // if you implement roles
    }

    /**
     * List all patients for the authenticated doctor.
     * Route: GET /api/patients
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $doctor = $this->doctorService->getDoctorForUser($user);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor profile not found for this user.',
            ], 404);
        }

        $patients = $this->patientService->getPatientsForDoctor($doctor);

        return response()->json([
            'data' => $patients,
        ]);
    }

    /**
     * Store a newly created patient associated to the authenticated doctor.
     * Route: POST /api/patients
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        $user = $request->user();
        $doctor = $this->doctorService->getDoctorForUser($user);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor profile not found for this user.',
            ], 404);
        }

        $validated = $request->validated();

        $patient = $this->patientService->createPatient($validated, $doctor);

        return response()->json([
            'message' => 'Patient created successfully.',
            'data'    => $patient,
        ], 201);
    }

    /**
     * Show a specific patient. You may later enforce that this patient
     * belongs to the authenticated doctor via doctor_patient relationship.
     * Route: GET /api/patients/{patient}
     */
    public function show(Request $request, Patient $patient): JsonResponse
    {
        // TODO: Optionally validate that the doctor is linked to this patient.

        $patient->load('user');

        return response()->json([
            'data' => $patient,
        ]);
    }

    /**
     * Update an existing patient.
     * Route: PUT /api/patients/{patient}
     */
    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        $validated = $request->validated();

        $patient = $this->patientService->updatePatient($patient, $validated);

        return response()->json([
            'message' => 'Patient updated successfully.',
            'data'    => $patient,
        ]);
    }
}
