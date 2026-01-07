<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorWorkingHourRequest;
use App\Http\Requests\UpdateDoctorWorkingHourRequest;
use App\Models\DoctorWorkingHour;
use App\Services\DoctorService;
use App\Services\DoctorWorkingHourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorWorkingHourController extends Controller
{
    protected DoctorService $doctorService;
    protected DoctorWorkingHourService $workingHourService;

    public function __construct(DoctorService $doctorService, DoctorWorkingHourService $workingHourService)
    {
        $this->doctorService = $doctorService;
        $this->workingHourService = $workingHourService;

        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor');
    }

    /**
     * List working hours for authenticated doctor.
     * GET /api/doctor-working-hours?consulting_room_id=1
     */
    public function index(Request $request): JsonResponse
    {
        $doctor = $this->doctorService->getDoctorForUser($request->user());

        if (!$doctor) {
            return response()->json(['message' => 'Doctor profile not found for this user.'], 404);
        }

        $consultingRoomId = $request->query('consulting_room_id')
            ? (int) $request->query('consulting_room_id')
            : null;

        $items = $this->workingHourService->list($doctor, $consultingRoomId);

        return response()->json(['data' => $items]);
    }

    /**
     * Create working hour for authenticated doctor.
     * POST /api/doctor-working-hours
     */
    public function store(StoreDoctorWorkingHourRequest $request): JsonResponse
    {
        $doctor = $this->doctorService->getDoctorForUser($request->user());

        if (!$doctor) {
            return response()->json(['message' => 'Doctor profile not found for this user.'], 404);
        }

        try {
            $item = $this->workingHourService->create($doctor, $request->validated());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Working hour created successfully.',
            'data'    => $item,
        ], 201);
    }

    /**
     * Update working hour.
     * PUT /api/doctor-working-hours/{doctorWorkingHour}
     */
    public function update(UpdateDoctorWorkingHourRequest $request, DoctorWorkingHour $doctorWorkingHour): JsonResponse
    {
        try {
            $item = $this->workingHourService->update($doctorWorkingHour, $request->validated());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Working hour updated successfully.',
            'data'    => $item,
        ]);
    }

    /**
     * Delete working hour.
     * DELETE /api/doctor-working-hours/{doctorWorkingHour}
     */
    public function destroy(DoctorWorkingHour $doctorWorkingHour): JsonResponse
    {
        $this->workingHourService->delete($doctorWorkingHour);

        return response()->json([
            'message' => 'Working hour deleted successfully.',
        ]);
    }
}
