<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\LocationEstimate;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();

        $alamatDariBooking = Booking::whereNotIn('status', ['selesai'])
            ->select('location_detail')
            ->distinct()
            ->orderBy('location_detail')
            ->pluck('location_detail')
            ->toArray();

        $alamatSudahTersimpan = Location::pluck('full_address')->toArray();

        $alamatList = array_values(array_diff($alamatDariBooking, $alamatSudahTersimpan));

        $estimasiList = LocationEstimate::with(['fromLocation', 'toLocation'])->get();

        return view('admin.lokasi-acara.index', compact('locations', 'alamatList', 'estimasiList'));
    }

    public function create()
    {
        $alamatDariBooking = Booking::distinct()->pluck('location')->toArray();
        $alamatSudahTersimpan = Location::pluck('full_address')->toArray();

        $alamatList = array_diff($alamatDariBooking, $alamatSudahTersimpan);

        return view('admin.lokasi-acara.create', compact('alamatList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_address' => 'required|string|max:1000',
        ]);

        $existing = Location::where('full_address', $validated['full_address'])->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Alamat ini sudah pernah ditambahkan.');
        }

        Location::create($validated);

        return redirect()->route('lokasi-acara.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }
}
