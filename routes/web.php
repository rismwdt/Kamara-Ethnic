<?php

use App\Models\Event;
use App\Services\ScheduleValidator;
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
use App\Http\Controllers\Admin\SchedulerController;
use App\Http\Controllers\Admin\ValidatorController;
use App\Http\Controllers\Klien\NominatimController;
use App\Http\Controllers\Admin\RecapPerformerController;
use App\Http\Controllers\Admin\LocationEstimateController;
use App\Http\Controllers\Admin\PerformerRequirementController;

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
    Route::resource('pesanan', PesananController::class)->except(['show']);
    Route::get('admin/pesanan/cetak', [PesananController::class, 'cetakPdf'])->name('admin.pesanan.cetak');
    // Route::get('pesanan/rekomendasi', [PesananController::class, 'rekomendasiHariIni'])->name('admin.pesanan.rekomendasi');
    // Route::get('scheduler/run', [SchedulerController::class, 'run'])->name('admin.scheduler.run');
    // Route::post('/validate-schedule', [ValidatorController::class, 'validateSchedule']);
    // Route::post('/api/validate-schedule', [ValidatorController::class, 'validateSchedule']);
    Route::get('rekap-pengisi-acara', [RecapPerformerController::class, 'index'])->name('admin.rekap-pengisi-acara');
    Route::resource('pengaturan-pengisi-acara', PerformerRequirementController::class)->except(['show']);
    Route::post('pesanan/cek-jadwal', [ValidatorController::class, 'cekJadwal'])
    ->name('pesanan.cek-jadwal');
});

//Klien
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/api/nominatim', [NominatimController::class, 'search'])->name('nominatim.search');
    Route::post('/cek-jadwal', [ScheduleController::class, 'checkSchedule'])->name('cek-jadwal');
    Route::post('/pesanan', [BookingController::class, 'store'])->name('booking.store');
});

require __DIR__.'/auth.php';
