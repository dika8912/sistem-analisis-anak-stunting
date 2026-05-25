<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;

// Halaman Publik
Route::get('/', function () {
    return view('halaman.beranda'); 
});

Route::get('/edukasi', function () {
    return view('halaman.edukasi'); 
});

// Auth (Hanya View, validasi dan submit ditangani oleh JS API)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
// Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
// Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');

// Admin (Proteksi Role dilakukan di sisi Client / JS)
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

// Halaman lain sesuai blueprint bisa ditambahkan nanti di sini
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Route::get('/detect', function () { return view('detect.index'); });