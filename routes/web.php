<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;


Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/search', [CertificateController::class, 'index'])->name('certificates.search_reload');
    Route::post('/certificates/search', [CertificateController::class, 'search'])->name('certificates.search');
    Route::get('/certificates/by-document', [CertificateController::class, 'index'])->name('certificates.by-document');
    Route::get('/api/documents/{document}', [CertificateController::class, 'getDocumentDetails']);
});
