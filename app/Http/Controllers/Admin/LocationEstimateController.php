<?php

namespace App\Http\Controllers\Admin;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\LocationEstimate;
use App\Http\Controllers\Controller;

class LocationEstimateController extends Controller
{
    public function index()
    {
        $estimasiList = LocationEstimate::with(['fromLocation', 'toLocation'])->get();
    $allLocations = Location::orderBy('full_address')->get();

    $existingPairs = LocationEstimate::all();
    $existingCombinations = [];

    foreach ($existingPairs as $pair) {
        $a = $pair->from_location_id;
        $b = $pair->to_location_id;
        $key = implode('-', collect([$a, $b])->sort()->toArray());
        $existingCombinations[$key] = true;
    }

    $filteredLocations = $allLocations->filter(function ($loc) use ($allLocations, $existingCombinations) {
        foreach ($allLocations as $other) {
            if ($loc->id !== $other->id) {
                $key = implode('-', collect([$loc->id, $other->id])->sort()->toArray());
                if (!isset($existingCombinations[$key])) {
                    return true;
                }
            }
        }
        return false;
    });

    return view('admin.lokasi-acara.index', [
        'estimasiList' => $estimasiList,
        'locations' => $filteredLocations,
        'allLocations' => $allLocations, // semua lokasi untuk modal edit
    ]);
    }


    public function create()
    {
        $locations = Location::orderBy('full_address')->get();

        // Ambil semua kombinasi dua arah yang sudah ada
        $existingPairs = LocationEstimate::all();
        $existingCombinations = [];

        foreach ($existingPairs as $pair) {
            $a = $pair->from_location_id;
            $b = $pair->to_location_id;
            $key = implode('-', collect([$a, $b])->sort()->toArray());
            $existingCombinations[$key] = true;
        }

        // Filter lokasi yang masih memiliki kombinasi yang belum diisi
        $filteredLocations = $locations->filter(function ($loc) use ($locations, $existingCombinations) {
            foreach ($locations as $other) {
                if ($loc->id !== $other->id) {
                    $key = implode('-', collect([$loc->id, $other->id])->sort()->toArray());
                    if (!isset($existingCombinations[$key])) {
                        return true;
                    }
                }
            }
            return false;
        });

        return view('admin.lokasi-acara.create', [
            'locations' => $filteredLocations,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_location_id' => 'required|exists:locations,id|different:to_location_id',
            'to_location_id'   => 'required|exists:locations,id',
            'distance_km'      => 'required|numeric|min:0',
            'estimated_mnt'    => 'required|integer|min:0',
        ]);

        $exists = LocationEstimate::where('from_location_id', $request->from_location_id)
                    ->where('to_location_id', $request->to_location_id)
                    ->exists();

        if ($exists) {
            return back()->withErrors('Estimasi jarak sudah ada untuk kombinasi lokasi tersebut.');
        }

        LocationEstimate::create($request->all());

        return redirect()->route('lokasi-acara.index')->with('success', 'Estimasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $estimate = LocationEstimate::findOrFail($id);
        $locations = Location::orderBy('name')->get();
        return view('admin.lokasi.edit', compact('estimate', 'locations'));
    }

    public function update(Request $request, $id)
    {
        $estimate = LocationEstimate::findOrFail($id);

        $request->validate([
            'from_location_id' => 'required|exists:locations,id|different:to_location_id',
            'to_location_id'   => 'required|exists:locations,id',
            'distance_km'      => 'required|numeric|min:0',
            'estimated_mnt'    => 'required|integer|min:0',
        ]);

        $estimate->update($request->all());

        return redirect()->route('lokasi.index')->with('success', 'Estimasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $estimate = LocationEstimate::findOrFail($id);
        $estimate->delete();
        return back()->with('success', 'Estimasi berhasil dihapus.');
    }
}
