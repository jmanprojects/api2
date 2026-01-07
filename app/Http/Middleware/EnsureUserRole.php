<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Ensure the authenticated user has at least one of the required roles.
     *
     * Roles are inferred from related profiles:
     * - doctor role => user has doctor profile
     * - nurse role  => user has nurse profile
     * - patient role=> user has patient profile
     *
     * Usage examples:
     *  ->middleware('role:doctor')
     *  ->middleware('role:doctor,nurse')
     *
     * IMPORTANT:
     * - This is MVP-friendly: no role table needed.
     * - Later you can swap this logic to use a roles column/table without changing routes.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // If there is no authenticated user, return 401.
        // (Normally auth:sanctum should prevent this, but we keep it safe.)
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        /**
         * Determine user "role" based on existing profiles.
         * NOTE: A user should typically have only one profile type in your system.
         */
        $isDoctor  = $user->relationLoaded('doctor')  ? (bool) $user->doctor  : (bool) $user->doctor()->exists();
        $isNurse   = $user->relationLoaded('nurse')   ? (bool) $user->nurse   : (bool) $user->nurse()->exists();
        $isPatient = $user->relationLoaded('patient') ? (bool) $user->patient : (bool) $user->patient()->exists();

        // Normalize required roles to lowercase
        $roles = array_map('strtolower', $roles);

        // If no roles were passed, allow by default (but ideally always pass roles).
        if (empty($roles)) {
            return $next($request);
        }

        // Check role match
        $allowed =
            (in_array('doctor', $roles, true)  && $isDoctor) ||
            (in_array('nurse', $roles, true)   && $isNurse) ||
            (in_array('patient', $roles, true) && $isPatient);

        if (!$allowed) {
            return response()->json([
                'message' => 'Forbidden. You do not have permission to perform this action.',
            ], 403);
        }

        return $next($request);
    }
}
