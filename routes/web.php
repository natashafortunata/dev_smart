<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PerhitunganController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    // Dapat diakses admin & pimpinan
    Route::get('/hasil', [HasilController::class, 'index'])->name('hasil.index');
    Route::get('/hasil/cetak', [HasilController::class, 'cetak'])->name('hasil.cetak');
    Route::get('/perhitungan', [PerhitunganController::class, 'index'])->name('perhitungan.index');

    // Khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('kriteria', KriteriaController::class)->parameters(['kriteria' => 'kriteria']);
        Route::post('/kriteria/{kriteria}/sub', [SubKriteriaController::class, 'store'])->name('sub-kriteria.store');
        Route::put('/sub-kriteria/{subKriteria}', [SubKriteriaController::class, 'update'])->name('sub-kriteria.update');
        Route::delete('/sub-kriteria/{subKriteria}', [SubKriteriaController::class, 'destroy'])->name('sub-kriteria.destroy');

        Route::resource('alternatif', AlternatifController::class)->except('show')->parameters(['alternatif' => 'alternatif']);

        Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/{alternatif}/edit', [PenilaianController::class, 'edit'])->name('penilaian.edit');
        Route::put('/penilaian/{alternatif}', [PenilaianController::class, 'update'])->name('penilaian.update');
        Route::delete('/penilaian/{alternatif}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');

        Route::post('/perhitungan/simpan', [PerhitunganController::class, 'simpan'])->name('perhitungan.simpan');

        Route::resource('users', UserController::class)->except('show');
    });
});
