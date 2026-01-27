<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNurseProfileRequest;
use App\Services\NurseProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NurseProfileController extends Controller
{
    public function __construct(
        protected NurseProfileService $nurseProfileService
    ) {}

    /**
     * GET /api/nurses/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $nurse = $this->nurseProfileService->getAuthenticatedNurseProfile($user);

        if (!$nurse) {
            return response()->json([
                'message' => 'Nurse profile not found for this user.',
            ], 404);
        }

        return response()->json([
            'data' => $nurse,
        ]);
    }

    /**
     * PUT /api/nurses/me
     * Creates nurse profile if missing (firstOrCreate) and updates it.
     */
    public function update(UpdateNurseProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $nurse = $this->nurseProfileService->updateNurseProfile($user, $validated);

        return response()->json([
            'message' => 'Nurse profile updated successfully.',
            'data'    => $nurse,
        ]);
    }
}
