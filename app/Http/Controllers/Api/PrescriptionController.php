<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Services\PrescriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    protected PrescriptionService $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;

        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor');
    }

    /**
     * List prescriptions for a patient or consultation.
     * Example route: GET /api/consultations/{consultation}/prescriptions
     */
    public function indexByConsultation(Consultation $consultation): JsonResponse
    {
        $prescriptions = $consultation->prescriptions()->with('items')->get();

        return response()->json([
            'data' => $prescriptions,
        ]);
    }

    /**
     * Create a new prescription for a consultation.
     * Route: POST /api/consultations/{consultation}/prescriptions
     */
    public function store(StorePrescriptionRequest $request, Consultation $consultation): JsonResponse
    {
        $validated = $request->validated();

        $prescription = $this->prescriptionService->createPrescription($consultation, $validated);

        return response()->json([
            'message' => 'Prescription created successfully.',
            'data'    => $prescription,
        ], 201);
    }

    /**
     * Show a specific prescription.
     * Route: GET /api/prescriptions/{prescription}
     */
    public function show(Prescription $prescription): JsonResponse
    {
        $prescription->load(['consultation', 'doctor.user', 'patient.user', 'items']);

        return response()->json([
            'data' => $prescription,
        ]);
    }

    /**
     * Update an existing prescription (replace items).
     * Route: PUT /api/prescriptions/{prescription}
     */
    public function update(UpdatePrescriptionRequest $request, Prescription $prescription): JsonResponse
    {
        $validated = $request->validated();

        $prescription = $this->prescriptionService->updatePrescription($prescription, $validated);

        return response()->json([
            'message' => 'Prescription updated successfully.',
            'data'    => $prescription,
        ]);
    }
}
