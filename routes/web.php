<?php

use App\Http\Controllers\DocumentPDFController;
use App\Http\Controllers\ImageEditorController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

// Image editor — admin only
Route::middleware('auth')->post('/admin/image-editor/save', [ImageEditorController::class, 'save'])->name('image-editor.save');
Route::middleware('auth')->get('/admin/documents/{document}/pdf', [DocumentPDFController::class, 'download'])->name('documents.pdf');
Route::post('/contact', [LandingController::class, 'contact'])->name('contact');

Route::prefix('portal')->name('portal.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    });

    Route::middleware(['auth', 'client.portal'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Invoices & Payment
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{id}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
        Route::get('invoices/{id}/pay/success', [InvoiceController::class, 'success'])->name('invoices.pay.success');
        Route::get('invoices/{id}/pay/failed', [InvoiceController::class, 'failed'])->name('invoices.pay.failed');
    });
});
