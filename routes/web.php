<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\ChildController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\EducationsController;

// Halaman Publik
Route::get('/', function () {
    return view('halaman.beranda'); 
});

Route::get('/edukasi', function () {
    return view('halaman.edukasi'); 
});

Route::get('/about', function () {
    return view('halaman.about'); 
})->name('about');

Route::get('/educations', [EducationsController::class, 'index'])->name('educations.index');
Route::get('/educations/{id}', [EducationsController::class, 'show'])->name('educations.show');

// Auth (Hanya View, validasi dan submit ditangani oleh JS API)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
// Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
// Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');

// Admin (Proteksi Role dilakukan di sisi Client / JS)
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/children', [AdminController::class, 'search'])->name('admin.children');
Route::get('/admin/educations', [EducationsController::class, 'adminForm'])->name('admin.educations');

// Halaman lain sesuai blueprint bisa ditambahkan nanti di sini
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Phase 5: Fitur Kalkulator Deteksi
use App\Http\Controllers\DetectController;

Route::get('/detect', [DetectController::class, 'index'])->name('detect.index');
Route::post('/detect/print', [DetectController::class, 'printPdf'])->name('detect.print');

// Phase 4: Manajemen Anak dan Pengukuran
Route::get('/children/create', [ChildController::class, 'create'])->name('children.create');
Route::get('/children/{id}', [ChildController::class, 'show'])->name('children.show');
Route::get('/measurements/create', [MeasurementController::class, 'create'])->name('measurements.create');