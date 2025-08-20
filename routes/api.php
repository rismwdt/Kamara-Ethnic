<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Klien\NominatimController;

Route::get('/nominatim', [NominatimController::class, 'search'])
    ->name('api.nominatim')
    ->middleware('throttle:30,1');
