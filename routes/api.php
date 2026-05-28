<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CaseController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\HearingController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\Client\CaseController as ClientCaseController;
use App\Http\Controllers\Api\V1\Client\HearingController as ClientHearingController;
use App\Http\Controllers\Api\V1\Client\InvoiceController as ClientInvoiceController;
use App\Http\Controllers\Api\V1\Client\ProfileController as ClientProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ─── Auth (public) ───────────────────────────────────────────────────────
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    // ─── Authenticated routes ─────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',     [AuthController::class, 'me']);

        Route::prefix('2fa')->group(function () {
            Route::post('enable',  [AuthController::class, 'enable2fa']);
            Route::post('verify',  [AuthController::class, 'verify2fa']);
            Route::post('disable', [AuthController::class, 'disable2fa']);
        });

        // ─── Lawyer / Admin routes ────────────────────────────────────────────
        Route::prefix('dashboard')->group(function () {
            Route::get('stats', [DashboardController::class, 'stats']);
        });

        Route::get('cases',          [CaseController::class, 'index']);
        Route::get('cases/{case}',   [CaseController::class, 'show']);

        Route::get('hearings',          [HearingController::class, 'index']);
        Route::get('hearings/upcoming', [HearingController::class, 'upcoming']);

        Route::get('clients',           [ClientController::class, 'index']);
        Route::get('clients/{client}',  [ClientController::class, 'show']);

        Route::get('invoices',          [InvoiceController::class, 'index']);
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);

        // ─── Client portal routes ─────────────────────────────────────────────
        Route::prefix('client')->group(function () {
            Route::get('profile',         [ClientProfileController::class, 'show']);
            Route::get('cases',           [ClientCaseController::class, 'index']);
            Route::get('cases/{id}',      [ClientCaseController::class, 'show']);
            Route::get('hearings',        [ClientHearingController::class, 'index']);
            Route::get('invoices',        [ClientInvoiceController::class, 'index']);
        });
    });
});
