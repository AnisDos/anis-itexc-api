<?php

use App\Http\Controllers\api\patient\AppointmentController;
use App\Http\Controllers\api\patient\AuthController;
use App\Http\Controllers\api\patient\DoctorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);




Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('doctors', DoctorController::class);

    Route::get('/getAvailabilityDates/{doctor}', [AppointmentController::class, 'getAvailabilityDates']);
});
