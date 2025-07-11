<?php

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
use App\Http\Controllers\Admin\ScheduleController;
// Controllers untuk role Kepala
use App\Http\Controllers\Kepala\DashboardController   as KepalaDashboardController;
use App\Http\Controllers\Kepala\ActivityController    as KepalaActivityController;
use App\Http\Controllers\Kepala\UserController        as KepalaUserController;
use App\Http\Controllers\Kepala\RequestController     as KepalaRequestController;
use App\Http\Controllers\Kepala\HarvestController     as KepalaHarvestController;
use App\Http\Controllers\Kepala\ScheduleController    as KepalaScheduleController;
// Controllers untuk role Pegawai
use App\Http\Controllers\Pegawai\UserController       as PegawaiUserController;
use App\Http\Controllers\Pegawai\RequestController    as PegawaiRequestController;
use App\Http\Controllers\Pegawai\HarvestController    as PegawaiHarvestController;
use App\Http\Controllers\Pegawai\ScheduleController   as PegawaiScheduleController;
use App\Http\Controllers\Pegawai\ActivityController   as PegawaiActivityController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;



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
        Route::resource('schedules', ScheduleController::class)->except('show');
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
        Route::resource('requests',   KepalaRequestController::class)->except('show');

        // Modul Hasil Panen (tim)
        Route::resource('harvests',   KepalaHarvestController::class)->except('show');

        // Modul Jadwal Kerja Harian (read-only)
        Route::resource('schedules',  KepalaScheduleController::class)->only(['index']);

        // Modul Ekspor Data (hanya data yang bisa dilihat Kepala)
        Route::get('activities/export', [KepalaActivityController::class, 'export'])
            ->name('activities.export');
        // Modul Ekspor Data Pegawai (cetak users)
        Route::get('users/export', [KepalaUserController::class, 'export'])
            ->name('users.export');
        // Modul Ekspor Permintaan Barang/Bahan (cetak requests)
        Route::get('requests/export', [KepalaRequestController::class, 'export'])
            ->name('requests.export');

        // Ekspor Hasil Panen (cetak harvests)
        Route::get('harvests/export', [KepalaHarvestController::class, 'export'])
            ->name('harvests.export');

        // Ekspor Jadwal Kerja Harian (cetak schedules)
        Route::get('schedules/export', [KepalaScheduleController::class, 'export'])
            ->name('schedules.export');
    });

// Pegawai only
Route::middleware(['auth', 'role:pegawai'])
    ->prefix('pegawai')
    ->name('pegawai.')
    ->group(function () {
        // Dashboard Pegawai
        Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');

        // Data Pegawai: Lihat profil sendiri
        Route::resource('users', PegawaiUserController::class)
            ->only(['show']);

        // Permintaan: Form buat, kirim, lihat status & riwayat
        Route::resource('requests', PegawaiRequestController::class)
            ->only(['index', 'show', 'create', 'store']);

        // Hasil Panen: Form input, kirim, lihat data sendiri
        Route::resource('harvests', PegawaiHarvestController::class)
            ->only(['index', 'show', 'create', 'store']);

        // Jadwal Kerja Harian: Lihat jadwal, tandai selesai
        Route::resource('schedules', PegawaiScheduleController::class)
            ->only(['index', 'update']);
        Route::patch(
            'schedules/{schedule}/complete',
            [PegawaiScheduleController::class, 'complete']
        )
            ->name('schedules.complete');


        // Ekspor Data (terbatas)
        Route::get('activities/export', [PegawaiActivityController::class, 'export'])
            ->name('activities.export');
        Route::get('requests/export', [PegawaiRequestController::class, 'export'])
            ->name('requests.export');
        Route::get('harvests/export', [PegawaiHarvestController::class, 'export'])
            ->name('harvests.export');
        Route::get('schedules/export', [PegawaiScheduleController::class, 'export'])
            ->name('schedules.export');
    });
