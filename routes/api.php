<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\BookingTransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('hospitals', HospitalController::class);
    Route::apiResource('specialists', SpecialistController::class);
});

Route::post('/hospitals/{hospital}/specialists', [HospitalController::class, 'attach']);
Route::delete('/hospitals/{hospital}/specialists/{specialist}', [HospitalController::class, 'detach']);

Route::apiResource('transactions', BookingTransactionController::class);
Route::patch('/transactions/{transaction}/status', [BookingTransactionController::class, 'updateStatus']);

Route::get('doctors-filter', [DoctorController::class, 'filterBySpecialist']);
Route::get('doctors/{doctorId}/available-times', [DoctorController::class, 'getAvailableTimes']);

Route::get('my-orders', [MyOrderController::class, 'index']);
Route::get('my-orders', [MyOrderController::class, 'store']);
Route::get('my-orders/{id}', [MyOrderController::class, 'show']);

// Route::get('/doctors', [DoctorController::class, 'index']);
// Route::get('/doctors/{id}', [DoctorController::class, 'show']);
