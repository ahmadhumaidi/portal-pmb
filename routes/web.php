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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetoranKampusController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

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
