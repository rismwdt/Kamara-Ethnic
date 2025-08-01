<?php

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Klien\BookingController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Klien\ScheduleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PerformerController;
use App\Http\Controllers\Admin\LocationEstimateController;

Route::get('/', function () {
    $events = Event::where('status', 'aktif')->get();
    return view('welcome', compact('events'));
})->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard');

//Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('tes-cetak', [PesananController::class, 'cetakPdf']);
//Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('pesanan/{pesanan}', [PesananController::class, 'show'])->name('admin.pesanan.show');
    Route::resource('pengisi-acara', PerformerController::class)->except(['show']);
    Route::resource('paket-acara', EventController::class)->except(['show']);
    Route::resource('lokasi-acara', LocationController::class)->except(['show']);
    Route::resource('estimasi', LocationEstimateController::class)->except(['show']);
    Route::resource('pesanan', PesananController::class)->except(['show']);
    Route::get('admin/pesanan/cetak', [PesananController::class, 'cetakPdf'])->name('admin.pesanan.cetak');
    Route::get('pesanan/rekomendasi', [PesananController::class, 'rekomendasiHariIni'])->name('admin.pesanan.rekomendasi');
});

//Klien
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::post('/cek-jadwal', [ScheduleController::class, 'checkSchedule'])->name('cek-jadwal');
    Route::post('/pesanan', [BookingController::class, 'store'])->name('booking.store');
});

require __DIR__.'/auth.php';
