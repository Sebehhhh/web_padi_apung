<?php

use App\Http\Controllers\ActivityPhotoController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityCategoryController;
use App\Http\Controllers\Admin\CropTypeController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ActivityPhotoController as AdminActivityPhotoController;
use App\Http\Controllers\Admin\RequestController;
// ...tambahkan Controller untuk role lain nanti

use Illuminate\Support\Facades\Route;

// Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin only (prefix dan middleware)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('activity-categories', ActivityCategoryController::class)->except(['show']);
    Route::resource('crop-types', CropTypeController::class)->except(['show']);
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::resource('activities', ActivityController::class)->except(['show']);
    Route::resource('activity-photos', AdminActivityPhotoController::class)->only(['store', 'destroy']);
    Route::resource('requests', RequestController::class)->except(['create', 'edit', 'update', 'show']);
    Route::post('requests/{request}/approve', [RequestController::class, 'approve'])->name('requests.approve');
    Route::post('requests/{request}/reject', [RequestController::class, 'reject'])->name('requests.reject');
});

// Jika nanti role 'kepala', 'pegawai', dll tinggal tambahkan group serupa:
# Route::middleware(['auth', 'role:kepala'])->prefix('kepala')->name('kepala.')->group(function () {
#     Route::get('/dashboard', [KepalaDashboardController::class, 'index'])->name('dashboard');
#     // dst...
# });
