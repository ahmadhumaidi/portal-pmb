<?php

use App\Http\Controllers\BerkasController;
use App\Http\Controllers\BiayaKampusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KampusController;
use App\Http\Controllers\LandingPageAdminController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LandingRegistrationController;
use App\Http\Controllers\KoordinatorController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\Portal\HasilController as PortalHasilController;
use App\Http\Controllers\Portal\KoordinatorAuthController;
use App\Http\Controllers\Portal\KoordinatorHasilController;
use App\Http\Controllers\Portal\MahasiswaAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetoranKampusController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::domain(config('portal.student_domain'))->name('portal.')->group(function () {
    Route::get('/', [MahasiswaAuthController::class, 'showLogin'])->name('mahasiswa.login');
    Route::post('/login', [MahasiswaAuthController::class, 'login'])->name('mahasiswa.login.attempt');
    Route::post('/logout', [MahasiswaAuthController::class, 'logout'])->name('mahasiswa.logout');
    Route::middleware('auth:mahasiswa')->group(function () {
        Route::get('/hasil', [PortalHasilController::class, 'index'])->name('mahasiswa.hasil');
        Route::get('/hasil/file/{field}', [PortalHasilController::class, 'viewFile'])->name('mahasiswa.hasil.file');
    });

    Route::get('/koordinator/login', [KoordinatorAuthController::class, 'showLogin'])->name('koordinator.login');
    Route::post('/koordinator/login', [KoordinatorAuthController::class, 'login'])->name('koordinator.login.attempt');
    Route::post('/koordinator/logout', [KoordinatorAuthController::class, 'logout'])->name('koordinator.logout');
    Route::middleware('auth:koordinator')->group(function () {
        Route::get('/koordinator/hasil', [KoordinatorHasilController::class, 'index'])->name('koordinator.hasil');
        Route::get('/koordinator/hasil/{mahasiswa}/file/{field}', [KoordinatorHasilController::class, 'viewFile'])->name('koordinator.hasil.file');
    });
});

Route::get('/', [LandingPageController::class, 'show'])->name('landing');
Route::post('/daftar', [LandingRegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('landing.register');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/landing-page/edit', [LandingPageAdminController::class, 'edit'])->name('landing.admin.edit');
    Route::put('/landing-page', [LandingPageAdminController::class, 'update'])->name('landing.admin.update');
    Route::get('/pendaftaran-landing', [LandingRegistrationController::class, 'index'])->name('landing.registrations.index');
    Route::get('/pendaftaran-landing', [LandingRegistrationController::class, 'index'])->name('landing.registrations.index');
    Route::get('/pendaftaran-landing', [LandingRegistrationController::class, 'index'])->name('landing.registrations.index');

    Route::resource('kampus', KampusController::class)
        ->parameters(['kampus' => 'kampus'])
        ->except('show');

    Route::resource('jurusan', JurusanController::class);

    Route::resource('biaya-kampus', BiayaKampusController::class)
        ->parameters(['biaya-kampus' => 'biayaKampus'])
        ->except('show');

    Route::resource('staff', StaffController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('koordinator', KoordinatorController::class);

    Route::get('mahasiswa-sampah', [MahasiswaController::class, 'trash'])->name('mahasiswa.trash');
    Route::post('mahasiswa/{mahasiswa}/restore', [MahasiswaController::class, 'restore'])->name('mahasiswa.restore')->withTrashed();
    Route::delete('mahasiswa/{mahasiswa}/force-delete', [MahasiswaController::class, 'forceDelete'])->name('mahasiswa.force-delete')->withTrashed();

    Route::resource('mahasiswa', MahasiswaController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::get('berkas/{berkas}/view/{field}', [BerkasController::class, 'viewFile'])->name('berkas.view-file');

    Route::resource('berkas', BerkasController::class)
        ->parameters(['berkas' => 'berkas'])
        ->only(['index', 'show', 'edit', 'update', 'destroy']);

    Route::get('ocr', [OcrController::class, 'index'])->name('ocr.index');
    Route::post('ocr/proses/{jenis}', [OcrController::class, 'proses'])->name('ocr.proses');
    Route::post('ocr/reset', [OcrController::class, 'reset'])->name('ocr.reset');
    Route::post('ocr/terapkan', [OcrController::class, 'terapkan'])->name('ocr.terapkan');

    Route::get('pembayaran/{pembayaran}/view/{field?}', [PembayaranController::class, 'viewFile'])->name('pembayaran.view-file');

    Route::resource('pembayaran', PembayaranController::class)
        ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::get('setoran-kampus/{setoranKampus}/view', [SetoranKampusController::class, 'viewFile'])->name('setoran-kampus.view-file');

    Route::resource('setoran-kampus', SetoranKampusController::class)
        ->parameters(['setoran-kampus' => 'setoranKampus']);

    Route::get('hasil/{hasil}/view/{field}', [HasilController::class, 'viewFile'])->name('hasil.view-file');

    Route::resource('hasil', HasilController::class)
        ->only(['index', 'show', 'edit', 'update']);

    Route::resource('laporan', LaporanController::class)
        ->only('index');

    Route::resource('pengaturan', PengaturanController::class)
        ->only('index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
