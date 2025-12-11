<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\UserController;





// Registro y login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('patients', PatientController::class);

    // Ruta personalizada para subir foto para pacientes
    Route::post('patients/{id}/foto', [PatientController::class, 'uploadFoto']);
});


// ruta personalizada para citas
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('appointments', AppointmentController::class);
});

//actualizar la  contraseña
Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/user/password', [UserController::class, 'updatePassword']);
});





    // Route::post('patients/{id}/foto', [PatientController::class, 'uploadFoto']);

