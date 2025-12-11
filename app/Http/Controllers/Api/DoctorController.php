<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorProfileRequest;
use App\Services\DoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * @var DoctorService
     *
     * We inject the DoctorService to keep the controller thin
     * and delegate business logic to the service layer.
     */
    protected DoctorService $doctorService;

    /**
     * Constructor with dependency injection.
     * Laravel will resolve DoctorService automatically.
     */
    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;

        // You can attach middleware here if needed, for example:
        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor'); // if you implement a role middleware
    }

    /**
     * Get the profile of the authenticated doctor.
     * Route example: GET /api/doctors/me
     *
     * Returns:
     *  - 200 with doctor data if the user is a doctor.
     *  - 404 if no doctor profile is found for this user.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $doctor = $this->doctorService->getAuthenticatedDoctorProfile($user);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor profile not found for this user.',
            ], 404);
        }

        return response()->json([
            'data' => $doctor,
        ]);
    }

    /**
     * Update the profile of the authenticated doctor.
     * Route example: PUT /api/doctors/me
     *
     * Uses UpdateDoctorProfileRequest for validation.
     * Delegates the update logic to DoctorService.
     */
    public function update(UpdateDoctorProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $doctor = $this->doctorService->updateDoctorProfile($user, $validated);

        return response()->json([
            'message' => 'Doctor profile updated successfully.',
            'data'    => $doctor,
        ]);
    }
}
