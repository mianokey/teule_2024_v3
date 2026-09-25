<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| M-Pesa C2B Callbacks
|--------------------------------------------------------------------------
|
| These endpoints will eventually be registered with Safaricom.
| They are intentionally named "payments" rather than "mpesa".
|
*/

Route::post('/payments/validation', [
    MpesaController::class,
    'validation',
]);

Route::post('/payments/confirmation', [
    MpesaController::class,
    'confirmation',
]);