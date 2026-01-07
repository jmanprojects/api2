<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserSettingsRequest;
use App\Services\UserSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * Service dependency injected for SRP:
     * Controller only orchestrates HTTP <-> Service calls.
     */
    protected UserSettingsService $userSettingsService;

    public function __construct(UserSettingsService $userSettingsService)
    {
        $this->userSettingsService = $userSettingsService;

        /**
         * We assume your routes are already inside auth:sanctum group.
         * If not, you can enable middleware here:
         *
         * $this->middleware('auth:sanctum');
         */
    }

    /**
     * Get the authenticated user profile payload.
     *
     * Route: GET /api/me
     *
     * Purpose:
     * - Frontend bootstrap: user info + theme + role-like profile detection.
     * - Allows SPA to decide which UI to show.
     *
     * Notes:
     * - We load doctor/nurse/patient relations explicitly.
     * - Role detection is derived from which profile exists (MVP-friendly).
     * - Later you can replace this with a real roles/permissions system.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        /**
         * Explicit eager loading prevents N+1 queries in the front.
         * Some of these relations will be null; that's expected.
         *
         * IMPORTANT:
         * - Ensure User model has doctor(), nurse(), patient() relations.
         */
        $user->load([
            'doctor',
            'nurse',
            'patient',
        ]);

        /**
         * Simple role inference for MVP:
         * - If doctor profile exists => doctor
         * - Else if nurse profile exists => nurse
         * - Else if patient profile exists => patient
         *
         * If later you support multiple profiles per user (rare), you'll change this.
         */
        $role = null;
        if ($user->doctor) {
            $role = 'doctor';
        } elseif ($user->nurse) {
            $role = 'nurse';
        } elseif ($user->patient) {
            $role = 'patient';
        }

        return response()->json([
            'data' => [
                'user' => $user,
                'role' => $role,
            ],
        ]);
    }

    /**
     * Update settings for the authenticated user.
     *
     * Route: PUT /api/me/settings
     *
     * Why a dedicated endpoint?
     * - Clean separation: settings changes are not mixed with profile updates.
     * - Extensible: add new settings fields later without impacting other flows.
     * - Security: request validates and service whitelists.
     */
    public function updateSettings(UpdateUserSettingsRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user = $this->userSettingsService->updateSettings($user, $validated);

        return response()->json([
            'message' => 'Settings updated successfully.',
            'data'    => $user,
        ]);
    }
}
