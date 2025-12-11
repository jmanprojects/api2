<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveConsultationDataRequest;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Services\ConsultationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    protected ConsultationService $consultationService;

    public function __construct(ConsultationService $consultationService)
    {
        $this->consultationService = $consultationService;

        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor');
    }

    /**
     * Start consultation for an appointment.
     * Route: POST /api/appointments/{appointment}/consultation/start
     */
    public function start(Request $request, Appointment $appointment): JsonResponse
    {
        $user = $request->user();

        $consultation = $this->consultationService->startConsultation($appointment, $user);

        return response()->json([
            'message' => 'Consultation started successfully.',
            'data'    => $consultation,
        ]);
    }

    /**
     * Save clinical data for a consultation.
     * Route: PUT /api/consultations/{consultation}
     */
    public function saveData(SaveConsultationDataRequest $request, Consultation $consultation): JsonResponse
    {
        $validated = $request->validated();

        $consultation = $this->consultationService->saveConsultationData($consultation, $validated);

        return response()->json([
            'message' => 'Consultation data saved successfully.',
            'data'    => $consultation,
        ]);
    }

    /**
     * Finish a consultation (mark ended and complete the appointment).
     * Route: POST /api/consultations/{consultation}/finish
     */
    public function finish(Request $request, Consultation $consultation): JsonResponse
    {
        $user = $request->user();

        $consultation = $this->consultationService->finishConsultation($consultation, $user);

        return response()->json([
            'message' => 'Consultation finished successfully.',
            'data'    => $consultation,
        ]);
    }

    /**
     * Show a consultation.
     * Route: GET /api/consultations/{consultation}
     */
    public function show(Consultation $consultation): JsonResponse
    {
        $consultation->load(['appointment', 'patient.user', 'doctor.user', 'preConsultation']);

        return response()->json([
            'data' => $consultation,
        ]);
    }
}
