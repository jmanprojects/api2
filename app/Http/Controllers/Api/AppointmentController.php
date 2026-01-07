<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Services\AppointmentService;
use App\Services\DoctorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\AppointmentQueryService;

class AppointmentController extends Controller
{
    protected AppointmentService $appointmentService;
    protected DoctorService $doctorService;

    public function __construct(AppointmentService $appointmentService, DoctorService $doctorService)
    {
        $this->appointmentService = $appointmentService;
        $this->doctorService      = $doctorService;

        // $this->middleware('auth:sanctum');
        // $this->middleware('role:doctor|nurse'); // si implementas roles
    }

    /**
     * List appointments for the authenticated doctor in a date range.
     * Route example: GET /api/appointments?from=2025-01-01&to=2025-01-31
     */
    // public function index(Request $request): JsonResponse
    // {
    //     $user   = $request->user();
    //     $doctor = $this->doctorService->getDoctorForUser($user);

    //     if (!$doctor) {
    //         return response()->json([
    //             'message' => 'Doctor profile not found for this user.',
    //         ], 404);
    //     }

    //     $from = $request->query('from')
    //         ? Carbon::parse($request->query('from'))
    //         : now()->startOfDay();

    //     $to = $request->query('to')
    //         ? Carbon::parse($request->query('to'))
    //         : now()->addDays(7)->endOfDay();

    //     $appointments = $this->appointmentService->getAppointmentsForDoctor($doctor, $from, $to);

    //     return response()->json([
    //         'data' => $appointments,
    //     ]);
    // }

        public function index(Request $request, AppointmentQueryService $queryService)
    {
        // 1) Policy: ¿puede acceder al módulo?
        $this->authorize('viewAny', Appointment::class);

        // 2) Scoped query: SOLO lo que le pertenece al usuario
        $appointments = $queryService
            ->forUser($request->user())
            ->paginate(15); // paginación pro para front

        return response()->json([
            'data' => $appointments,
        ]);
    }

    /**
     * Create a new appointment for the authenticated doctor.
     * Route: POST /api/appointments
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $user   = $request->user();
        $doctor = $this->doctorService->getDoctorForUser($user);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor profile not found for this user.',
            ], 404);
        }

        $data = $request->validated();

        $patient = Patient::findOrFail($data['patient_id']);

        try {
            $appointment = $this->appointmentService->createAppointment(
                $data,
                $doctor,
                $patient,
                $data['consulting_room_id'],
                $user->id
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Appointment created successfully.',
            'data'    => $appointment,
        ], 201);
    }

    /**
     * Show a specific appointment.
     * Route: GET /api/appointments/{appointment}
     */
    public function show(Appointment $appointment): JsonResponse
    {
        $appointment->load(['doctor.user', 'patient.user', 'consultingRoom', 'statusHistory']);

        return response()->json([
            'data' => $appointment,
        ]);
    }

    /**
     * Update the status of an appointment.
     * Route: PUT /api/appointments/{appointment}/status
     */
    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment): JsonResponse
    {
        $user    = $request->user();
        $data    = $request->validated();
        $status  = $data['status'];
        $reason  = $data['reason'] ?? null;

        $appointment = $this->appointmentService->changeStatus($appointment, $status, $user->id, $reason);

        return response()->json([
            'message' => 'Appointment status updated successfully.',
            'data'    => $appointment,
        ]);
    }
}
