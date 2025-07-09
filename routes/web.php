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
use App\Http\Controllers\Admin\HarvestController;
use App\Http\Controllers\Admin\RequestController;

// Controllers untuk role Kepala
use App\Http\Controllers\Kepala\DashboardController   as KepalaDashboardController;
use App\Http\Controllers\Kepala\ActivityController    as KepalaActivityController;
use App\Http\Controllers\Kepala\UserController        as KepalaUserController;
use App\Http\Controllers\Kepala\RequestController     as KepalaRequestController;
use App\Http\Controllers\Kepala\HarvestController     as KepalaHarvestController;
use App\Http\Controllers\Kepala\ScheduleController    as KepalaScheduleController;
use App\Http\Controllers\Kepala\ExportController      as KepalaExportController;

use Illuminate\Support\Facades\Route;

// Login & Logout
Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login',   [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin only
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users',               UserController::class);
        Route::resource('activity-categories', ActivityCategoryController::class)->except('show');
        Route::resource('crop-types',          CropTypeController::class)->except('show');
        Route::get('logs',   [LogController::class, 'index'])->name('logs.index');
        Route::resource('activities',          ActivityController::class)->except('show');
        Route::resource('activity-photos',     AdminActivityPhotoController::class)->only('store', 'destroy');
        Route::resource('requests',            RequestController::class)->except(['create', 'edit', 'update', 'show']);
        Route::post('requests/{request}/approve', [RequestController::class, 'approve'])->name('requests.approve');
        Route::post('requests/{request}/reject',  [RequestController::class, 'reject'])->name('requests.reject');
        Route::resource('harvests',            HarvestController::class)->except('show');
    });

// Kepala only
Route::middleware(['auth', 'role:kepala'])
    ->prefix('kepala')
    ->name('kepala.')
    ->group(function () {
        // Dashboard Kepala
        Route::get('/dashboard', [KepalaDashboardController::class, 'index'])->name('dashboard');

        // Modul Kegiatan (read-only)
        Route::resource('activities', KepalaActivityController::class)->except('show');

        // Modul Data Pegawai (profil tim)
        Route::resource('users', KepalaUserController::class)->except('show');

        // Modul Permintaan (ringkasan & histori)
        Route::resource('requests',   KepalaRequestController::class)->only(['index', 'show']);

        // Modul Hasil Panen (tim)
        Route::resource('harvests',   KepalaHarvestController::class)->only(['index', 'show']);

        // Modul Jadwal Kerja Harian (read-only)
        Route::resource('schedules',  KepalaScheduleController::class)->only(['index']);

        // Modul Ekspor Data (hanya data yang bisa dilihat Kepala)
        Route::get('activities/export', [KepalaActivityController::class, 'export'])
            ->name('activities.export');
        // Modul Ekspor Data Pegawai (cetak users)
        Route::get('users/export', [KepalaUserController::class, 'export'])
            ->name('users.export');
    });
