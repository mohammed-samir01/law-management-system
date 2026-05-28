<?php

use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\InvoicePdfController;
use App\Http\Controllers\Mobile\PaymentController;
use App\Http\Controllers\Mobile\PushController;
use App\Http\Controllers\Mobile\Client\CaseController as ClientCaseController;
use App\Http\Controllers\Mobile\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Mobile\Client\HearingController as ClientHearingController;
use App\Http\Controllers\Mobile\Client\InvoiceController as ClientInvoiceController;
use App\Http\Controllers\Mobile\Lawyer\CaseController as LawyerCaseController;
use App\Http\Controllers\Mobile\Lawyer\DocumentController as LawyerDocumentController;
use App\Http\Controllers\Mobile\Lawyer\AiController;
use App\Http\Controllers\Mobile\Lawyer\DocumentViewController;
use App\Http\Controllers\Mobile\Lawyer\ClientController as LawyerClientController;
use App\Http\Controllers\Mobile\Lawyer\DashboardController as LawyerDashboardController;
use App\Http\Controllers\Mobile\Lawyer\HearingController as LawyerHearingController;
use App\Http\Controllers\Mobile\Lawyer\InvoiceController as LawyerInvoiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->name('mobile.')->group(function () {

    // ─── Auth (guest) ──────────────────────────────────────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.post');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
    Route::post('push/token', [PushController::class, 'saveToken'])->name('push.token')->middleware('auth');

    // ─── Lawyer routes ─────────────────────────────────────────────────────────
    Route::middleware(['auth'])->prefix('lawyer')->name('lawyer.')->group(function () {
        Route::get('dashboard', [LawyerDashboardController::class, 'index'])->name('dashboard');

        Route::get('cases',       [LawyerCaseController::class, 'index'])->name('cases');
        Route::get('cases/{id}',  [LawyerCaseController::class, 'show'])->name('cases.show');
        Route::get('cases/{id}/documents',        [LawyerDocumentController::class, 'index'])->name('cases.documents');
        Route::post('cases/{id}/documents/upload',[LawyerDocumentController::class, 'upload'])->name('cases.documents.upload');

        Route::get('hearings',    [LawyerHearingController::class, 'index'])->name('hearings');

        Route::get('clients',     [LawyerClientController::class, 'index'])->name('clients');
        Route::get('clients/{id}',[LawyerClientController::class, 'show'])->name('clients.show');

        Route::get('invoices',       [LawyerInvoiceController::class, 'index'])->name('invoices');
        Route::get('invoices/{id}',  [LawyerInvoiceController::class, 'show'])->name('invoices.show');

        Route::get('cases/{caseId}/documents/{docId}/view', [DocumentViewController::class, 'show'])->name('cases.documents.view');

        // ─── AI routes ────────────────────────────────────────────────────────
        Route::post('cases/{id}/ai/summary',                         [AiController::class, 'caseSummary'])->name('cases.ai.summary');
        Route::post('cases/{caseId}/documents/{docId}/ai/analyze',   [AiController::class, 'analyzeDocument'])->name('cases.documents.ai.analyze');
        Route::post('hearings/{id}/ai/reminder',                     [AiController::class, 'generateReminder'])->name('hearings.ai.reminder');
        Route::post('hearings/{id}/ai/send-reminder',                [AiController::class, 'sendReminder'])->name('hearings.ai.send');
    });

    // ─── Client routes ─────────────────────────────────────────────────────────
    Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
        Route::get('dashboard',   [ClientDashboardController::class, 'index'])->name('dashboard');

        Route::get('cases',       [ClientCaseController::class, 'index'])->name('cases');
        Route::get('cases/{id}',  [ClientCaseController::class, 'show'])->name('cases.show');

        Route::get('hearings',    [ClientHearingController::class, 'index'])->name('hearings');

        Route::get('invoices',      [ClientInvoiceController::class, 'index'])->name('invoices');
        Route::get('invoices/{id}', [ClientInvoiceController::class, 'show'])->name('invoices.show');
    });

    // ─── Invoice PDF & Payment (shared, auth required) ─────────────────────────
    Route::middleware(['auth'])->group(function () {
        Route::get('invoices/{id}/pdf',          [InvoicePdfController::class, 'show'])->name('invoices.pdf');
        Route::get('invoices/{id}/pay',          [PaymentController::class, 'show'])->name('invoices.pay.show');
        Route::post('invoices/{id}/pay',         [PaymentController::class, 'initiate'])->name('invoices.pay.initiate');
        Route::get('invoices/{id}/pay/success',  [PaymentController::class, 'success'])->name('invoices.pay.success');
        Route::get('invoices/{id}/pay/failed',   [PaymentController::class, 'failed'])->name('invoices.pay.failed');
    });
});
