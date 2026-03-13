<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SchoolDayController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['status' => 'ok', 'message' => 'API is running.']));

Route::middleware(['ensure.https', 'frontend.api'])->group(function (): void {
    Route::get('/login', fn () => response()->json([
        'message' => 'Use POST /api/login with {"email","password"} to authenticate.',
    ], 405));
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::apiResource('students', StudentController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('school-days', SchoolDayController::class);
        Route::get('/weather', [WeatherController::class, 'index']);
    });
});