<?php

use App\Models\Event;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Klien\BookingController;
use App\Http\Controllers\Klien\ScheduleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PerformerController;
use App\Http\Controllers\Admin\ValidatorController;
use App\Http\Controllers\Admin\PerformerRoleController;
use App\Http\Controllers\Admin\PerformerUserController;
use App\Http\Controllers\Admin\OptimasiJadwalController;
use App\Http\Controllers\Admin\PerformerRequirementController;

Route::get('/', function () {
    $events = Event::where('status', 'aktif')->get();
    return view('welcome', compact('events'));
})->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin|owner'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin|owner'])->prefix('admin')->group(function () {
    Route::resource('paket-acara', EventController::class)->except(['show']);

    Route::resource('pengisi-acara', PerformerController::class)->except(['show']);

    Route::resource('pesanan', PesananController::class)->except(['show']);
    Route::get('pesanan/{pesanan}', [PesananController::class, 'show'])->name('admin.pesanan.show');

    Route::get('pesanan/tambah-pengisi-acara', [PesananController::class,'tambahPengisiAcara'])
        ->name('pesanan.tambah-pengisi-acara');
    Route::post('pesanan/tambah-pengisi-acara', [PesananController::class,'simpanPengisiAcaraManual'])
        ->name('pesanan.tambah-pengisi-acara.store');

    Route::post('pesanan/cek-jadwal', [ValidatorController::class, 'cekJadwal'])
        ->name('pesanan.cek-jadwal');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::resource('peran', PerformerRoleController::class)->except(['show']);

    Route::resource('pengaturan-pengisi-acara', PerformerRequirementController::class)->except(['show']);
    Route::resource('pengaturan-paket-acara', PerformerRequirementController::class)->except(['show']);
    Route::delete('pengaturan-paket-acara/event/{event}', [PerformerRequirementController::class,'destroyByEvent'])
        ->name('pengaturan-paket-acara.destroy-event');

    Route::resource('akun', PerformerUserController::class)->except(['show']);
    Route::put('akun/{akun}/password', [PerformerUserController::class, 'updatePassword'])
        ->name('akun.password.update');

    Route::get('pesanan/cetak', [PesananController::class, 'cetakPdf'])->name('admin.pesanan.cetak');
});


Route::middleware(['auth','role:performer'])->group(function () {
    Route::get('/performer/dashboard', [\App\Http\Controllers\Performer\DashboardController::class, 'index'])
        ->name('performer.dashboard');
});


Route::middleware(['auth', 'role:client'])->group(function () {
    Route::post('/cek-jadwal', [ScheduleController::class, 'checkSchedule'])->name('cek-jadwal');
    Route::post('/pesanan', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/pesanan-saya', [BookingController::class, 'myOrders'])->name('booking.my');
    Route::get('/pesanan/{booking}/invoice', [BookingController::class, 'invoice'])->name('booking.invoice');
});

require __DIR__.'/auth.php';
