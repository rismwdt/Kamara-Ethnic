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

        $alamatList = Booking::whereNotIn('status', ['selesai'])
            ->select('location_detail')
            ->distinct()
            ->orderBy('location_detail')
            ->pluck('location_detail');

        $estimasiList = LocationEstimate::with(['fromLocation', 'toLocation'])->get();

        return view('admin.lokasi-acara.index', compact('locations', 'alamatList', 'estimasiList'));
    }

    public function create()
    {
        $alamatList = Booking::whereNotIn('status', ['selesai'])
            ->select('location_detail')
            ->distinct()
            ->orderBy('location_detail')
            ->pluck('location_detail');


        return view('admin.lokasi-acara.create', compact('alamatList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'full_address' => 'required|string|unique:locations,full_address',
        ]);

        Location::create([
            'name' => $request->name,
            'full_address' => $request->full_address,
        ]);

        return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan.');
    }
}
