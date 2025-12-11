<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavePreConsultationRequest;
use App\Models\Appointment;
use App\Services\PreConsultationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreConsultationController extends Controller
{
    protected PreConsultationService $preConsultationService;

    public function __construct(PreConsultationService $preConsultationService)
    {
        $this->preConsultationService = $preConsultationService;

        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor|nurse');
    }

    /**
     * Create or update preconsultation data for an appointment.
     * Route example: POST /api/appointments/{appointment}/pre-consultation
     */
    public function save(SavePreConsultationRequest $request, Appointment $appointment): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $preConsultation = $this->preConsultationService->savePreConsultation($appointment, $data, $user);

        return response()->json([
            'message' => 'Preconsultation saved successfully.',
            'data'    => $preConsultation,
        ]);
    }

    /**
     * Show preconsultation for an appointment.
     * Route example: GET /api/appointments/{appointment}/pre-consultation
     */
    public function show(Appointment $appointment): JsonResponse
    {
        $preConsultation = $appointment->preConsultation;

        if (!$preConsultation) {
            return response()->json([
                'message' => 'Preconsultation not found for this appointment.',
            ], 404);
        }

        return response()->json([
            'data' => $preConsultation,
        ]);
    }
}
