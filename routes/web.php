<?php

use App\Http\Controllers\BerkasController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KampusController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::resource('kampus', KampusController::class)
    ->parameters(['kampus' => 'kampus'])
    ->except('show');

Route::resource('jurusan', JurusanController::class)
    ->except('show');

Route::resource('staff', StaffController::class)
    ->only(['index', 'create', 'store']);

Route::resource('mahasiswa', MahasiswaController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

Route::get('berkas/{berkas}/view/{field}', [BerkasController::class, 'viewFile'])->name('berkas.view-file');

Route::resource('berkas', BerkasController::class)
    ->parameters(['berkas' => 'berkas'])
    ->only(['index', 'show', 'edit', 'update']);

Route::get('pembayaran/{pembayaran}/view', [PembayaranController::class, 'viewFile'])->name('pembayaran.view-file');

Route::resource('pembayaran', PembayaranController::class)
    ->only(['index', 'show', 'create', 'store']);

Route::get('hasil/{hasil}/view/{field}', [HasilController::class, 'viewFile'])->name('hasil.view-file');

Route::resource('hasil', HasilController::class)
    ->only(['index', 'show', 'edit', 'update']);

Route::resource('laporan', LaporanController::class)
    ->only('index');

Route::resource('pengaturan', PengaturanController::class)
    ->only('index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


