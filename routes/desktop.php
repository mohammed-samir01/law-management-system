<?php

use App\Http\Controllers\Desktop\AuthController as DesktopAuthController;
use App\Http\Controllers\Desktop\CalendarController;
use App\Http\Controllers\Desktop\CaseController;
use App\Http\Controllers\Desktop\ClientController;
use App\Http\Controllers\Desktop\DashboardController;
use App\Http\Controllers\Desktop\DocumentController;
use App\Http\Controllers\Desktop\InvoiceController;
use App\Http\Controllers\Desktop\ReportController;
use Illuminate\Support\Facades\Route;

// ─── Desktop Auth (guest) ──────────────────────────────────────────────────
Route::prefix('desktop')->name('desktop.')->middleware('guest')->group(function () {
    Route::get('login',  [DesktopAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [DesktopAuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
});

Route::prefix('desktop')->name('desktop.')->middleware('auth')->group(function () {
    Route::post('logout', [DesktopAuthController::class, 'logout'])->name('logout');

    Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');
    Route::get('cases',      [CaseController::class,      'index'])->name('cases');
    Route::get('cases/{id}', [CaseController::class,      'show'])->name('cases.show');
    Route::get('calendar',   [CalendarController::class,  'index'])->name('calendar');
    Route::get('clients',    [ClientController::class,    'index'])->name('clients');
    Route::get('documents',           [DocumentController::class, 'index'])->name('documents');
    Route::get('documents/{id}/view', [DocumentController::class, 'view'])->name('documents.view');
    Route::get('invoices',            [InvoiceController::class,  'index'])->name('invoices');
    Route::get('invoices/{id}/pdf',   [InvoiceController::class,  'pdf'])->name('invoices.pdf');
    Route::get('reports',             [ReportController::class,   'index'])->name('reports');
});
