<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\AppointmentController;

// Controllers (API)
use App\Http\Controllers\Api\NurseController;
use App\Http\Controllers\Api\PreConsultationController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\PrescriptionController;

use App\Http\Controllers\Api\DoctorWorkingHourController;
use App\Http\Controllers\Api\DoctorTimeOffController;

use App\Http\Controllers\Api\MeController;

use App\Http\Controllers\Api\PrescriptionPdfController;




// Registro y login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);







/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here we register routes for the "core" consultorio system.
| Auth routes are assumed to be defined elsewhere (as you mentioned).
|
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Patients
    |--------------------------------------------------------------------------
    |
    | Basic patient management from the doctor-side (or nurse-side later).
    |
    */
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{patient}', [PatientController::class, 'show']);
    Route::put('/patients/{patient}', [PatientController::class, 'update']);


    /*
    |--------------------------------------------------------------------------
    | Nurses
    |--------------------------------------------------------------------------
    |
    | Nurse management (later we can scope this by clinic/consulting room/doctor).
    |
    */
    Route::get('/nurses', [NurseController::class, 'index']);
    Route::post('/nurses', [NurseController::class, 'store']);
    Route::get('/nurses/{nurse}', [NurseController::class, 'show']);
    Route::put('/nurses/{nurse}', [NurseController::class, 'update']);


    /*
    |--------------------------------------------------------------------------
    | Appointments
    |--------------------------------------------------------------------------
    |
    | Scheduling and management of appointments.
    |
    */
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);

    // Status transitions (scheduled -> confirmed -> in_preconsultation -> in_consultation -> completed, etc.)
    Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);


    /*
    |--------------------------------------------------------------------------
    | Pre-consultation (triage)
    |--------------------------------------------------------------------------
    |
    | Saved under an appointment. One preconsultation per appointment.
    |
    */
    Route::get('/appointments/{appointment}/pre-consultation', [PreConsultationController::class, 'show']);
    Route::post('/appointments/{appointment}/pre-consultation', [PreConsultationController::class, 'save']);


    /*
    |--------------------------------------------------------------------------
    | Consultation
    |--------------------------------------------------------------------------
    |
    | The main medical consultation is started from an appointment.
    | Then the consultation entity is updated and later finished.
    |
    */
    Route::post('/appointments/{appointment}/consultation/start', [ConsultationController::class, 'start']);

    Route::get('/consultations/{consultation}', [ConsultationController::class, 'show']);
    Route::put('/consultations/{consultation}', [ConsultationController::class, 'saveData']);
    Route::post('/consultations/{consultation}/finish', [ConsultationController::class, 'finish']);


    /*
    |--------------------------------------------------------------------------
    | Prescriptions
    |--------------------------------------------------------------------------
    |
    | Prescriptions are created under a consultation, and can be updated later.
    |
    */
    Route::get('/consultations/{consultation}/prescriptions', [PrescriptionController::class, 'indexByConsultation']);
    Route::post('/consultations/{consultation}/prescriptions', [PrescriptionController::class, 'store']);

    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show']);
    Route::put('/prescriptions/{prescription}', [PrescriptionController::class, 'update']);


        // Doctor schedule (working hours)
    Route::get('/doctor-working-hours', [DoctorWorkingHourController::class, 'index']);
    Route::post('/doctor-working-hours', [DoctorWorkingHourController::class, 'store']);
    Route::put('/doctor-working-hours/{doctorWorkingHour}', [DoctorWorkingHourController::class, 'update']);
    Route::delete('/doctor-working-hours/{doctorWorkingHour}', [DoctorWorkingHourController::class, 'destroy']);

    // Doctor time off (exceptions/blocks)
    Route::get('/doctor-time-offs', [DoctorTimeOffController::class, 'index']);
    Route::post('/doctor-time-offs', [DoctorTimeOffController::class, 'store']);
    Route::put('/doctor-time-offs/{doctorTimeOff}', [DoctorTimeOffController::class, 'update']);
    Route::delete('/doctor-time-offs/{doctorTimeOff}', [DoctorTimeOffController::class, 'destroy']);

    Route::get('/me', [MeController::class, 'show']);
    Route::put('/me/settings', [MeController::class, 'updateSettings']);


        
    // PDF (doctor only for MVP)
    Route::get('/prescriptions/{prescription}/pdf', [PrescriptionPdfController::class, 'stream'])
        ->middleware('role:doctor');

    Route::get('/prescriptions/{prescription}/pdf/download', [PrescriptionPdfController::class, 'download'])
        ->middleware('role:doctor');


});














// Rutas protegidas
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/profile', [AuthController::class, 'profile']);
//     Route::post('/logout', [AuthController::class, 'logout']);
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('patients', PatientController::class);

//     Route::post('patients/{id}/foto', [PatientController::class, 'uploadFoto']);
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('appointments', AppointmentController::class);
// });



    // Route::post('patients/{id}/foto', [PatientController::class, 'uploadFoto']);

