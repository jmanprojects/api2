<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorTimeOffRequest;
use App\Http\Requests\UpdateDoctorTimeOffRequest;
use App\Models\DoctorTimeOff;
use App\Services\DoctorService;
use App\Services\DoctorTimeOffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorTimeOffController extends Controller
{
    protected DoctorService $doctorService;
    protected DoctorTimeOffService $timeOffService;

    public function __construct(DoctorService $doctorService, DoctorTimeOffService $timeOffService)
    {
        $this->doctorService = $doctorService;
        $this->timeOffService = $timeOffService;

        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor');
    }

    /**
     * List time off records for authenticated doctor.
     * GET /api/doctor-time-offs?consulting_room_id=1
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

        $items = $this->timeOffService->list($doctor, $consultingRoomId);

        return response()->json(['data' => $items]);
    }

    /**
     * Create time off record.
     * POST /api/doctor-time-offs
     */
    public function store(StoreDoctorTimeOffRequest $request): JsonResponse
    {
        $doctor = $this->doctorService->getDoctorForUser($request->user());

        if (!$doctor) {
            return response()->json(['message' => 'Doctor profile not found for this user.'], 404);
        }

        try {
            $item = $this->timeOffService->create($doctor, $request->validated());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Time off created successfully.',
            'data'    => $item,
        ], 201);
    }

    /**
     * Update time off record.
     * PUT /api/doctor-time-offs/{doctorTimeOff}
     */
    public function update(UpdateDoctorTimeOffRequest $request, DoctorTimeOff $doctorTimeOff): JsonResponse
    {
        try {
            $item = $this->timeOffService->update($doctorTimeOff, $request->validated());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Time off updated successfully.',
            'data'    => $item,
        ]);
    }

    /**
     * Delete time off record.
     * DELETE /api/doctor-time-offs/{doctorTimeOff}
     */
    public function destroy(DoctorTimeOff $doctorTimeOff): JsonResponse
    {
        $this->timeOffService->delete($doctorTimeOff);

        return response()->json([
            'message' => 'Time off deleted successfully.',
        ]);
    }
}
