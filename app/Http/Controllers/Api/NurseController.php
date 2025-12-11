<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNurseRequest;
use App\Http\Requests\UpdateNurseRequest;
use App\Models\Nurse;
use App\Services\NurseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NurseController extends Controller
{
    protected NurseService $nurseService;

    public function __construct(NurseService $nurseService)
    {
        $this->nurseService = $nurseService;

        // Example middlewares:
        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor'); // if you implement role-based access
    }

    /**
     * List all nurses.
     * You might later scope this by clinic/doctor.
     * Route: GET /api/nurses
     */
    public function index(): JsonResponse
    {
        $nurses = Nurse::with('user')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return response()->json([
            'data' => $nurses,
        ]);
    }

    /**
     * Store a newly created nurse.
     * Route: POST /api/nurses
     */
    public function store(StoreNurseRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $nurse = $this->nurseService->createNurse($validated);

        return response()->json([
            'message' => 'Nurse created successfully.',
            'data'    => $nurse,
        ], 201);
    }

    /**
     * Show a specific nurse.
     * Route: GET /api/nurses/{nurse}
     */
    public function show(Nurse $nurse): JsonResponse
    {
        $nurse->load('user');

        return response()->json([
            'data' => $nurse,
        ]);
    }

    /**
     * Update an existing nurse.
     * Route: PUT /api/nurses/{nurse}
     */
    public function update(UpdateNurseRequest $request, Nurse $nurse): JsonResponse
    {
        $validated = $request->validated();

        $nurse = $this->nurseService->updateNurse($nurse, $validated);

        return response()->json([
            'message' => 'Nurse updated successfully.',
            'data'    => $nurse,
        ]);
    }
}
