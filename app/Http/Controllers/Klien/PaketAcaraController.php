<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Models\Event;

class PaketAcaraController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'aktif')->get(); 
        return view('klien.paket-acara', compact('events'));
    }
}
