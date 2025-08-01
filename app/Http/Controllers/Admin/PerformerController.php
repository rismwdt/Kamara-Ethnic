<?php

namespace App\Http\Controllers\Admin;

use App\Models\Performer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PerformerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $performers = Performer::oldest()->paginate(10);
        return view('admin.pengisi-acara.index', compact('performers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pengisi-acara.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:laki-laki,perempuan,lainnya',
            'category' => 'required|string|max:100',
            'phone' => 'required|string|max:100',
            'account_number' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'aktif';

        Performer::create($request->all());

        return redirect()->route('pengisi-acara.index')->with('success', 'Pengisi acara berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $performer = Performer::findOrFail($id);
        return view('admin.pengisi-acara.edit', compact('performer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $performer = Performer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:laki-laki,perempuan',
            'category' => 'required|string|max:100',
            'phone' => 'required|string|max:100',
            'account_number' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
            'notes' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $performer->update($request->all());

        return redirect()->route('pengisi-acara.index')->with('success', 'Pengisi acara berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $performer = Performer::findOrFail($id);
        $performer->delete();

        return redirect()->route('pengisi-acara.index')->with('success', 'Pengisi acara berhasil dihapus.');
    }
}
