<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MpesaController;
use App\Http\Controllers\Api\MpesaCollectorDeviceController;
use App\Http\Controllers\Api\MpesaCollectorMessageSyncController;

// Existing Sanctum user endpoint
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Existing Safaricom C2B callbacks
Route::post('/payments/validation', [
    MpesaController::class,
    'validation',
]);

Route::post('/payments/confirmation', [
    MpesaController::class,
    'confirmation',
]);

// =========================================================
// TEULE M-PESA SMS COLLECTOR API
// =========================================================

// Device registration
Route::post(
    '/mpesa-collector/devices/register',
    [MpesaCollectorDeviceController::class, 'register']
)->middleware('throttle:10,1');

// Upload collected SMS messages
Route::post(
    '/mpesa-collector/messages/sync',
    [MpesaCollectorMessageSyncController::class, 'sync']
)->middleware(['mpesa.collector.auth', 'throttle:30,1']);

// Authenticated device status
Route::get(
    '/mpesa-collector/device/status',
    [MpesaCollectorDeviceController::class, 'status']
)->middleware(['mpesa.collector.auth', 'throttle:30,1']);

// Authenticated device heartbeat
Route::post(
    '/mpesa-collector/device/heartbeat',
    [MpesaCollectorDeviceController::class, 'heartbeat']
)->middleware(['mpesa.collector.auth', 'throttle:30,1']);

Route::get(
    '/mpesa-collector/device/dashboard',
    [MpesaCollectorDeviceController::class, 'dashboard']
)->middleware(['mpesa.collector.auth', 'throttle:30,1']);