<?php

namespace App\Http\Controllers\Admin;

use App\Models\Performer;
use Illuminate\Http\Request;
use App\Models\PerformerRole;
use App\Http\Controllers\Controller;

class PerformerController extends Controller
{

    public function index()
    {
        $performers = Performer::oldest()->paginate(10);
        return view('admin.pengisi-acara.index', compact('performers'));
    }

    public function create()
    {
        $roles = PerformerRole::all();
        return view('admin.pengisi-acara.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:laki-laki,perempuan,lainnya',
            'performer_role_id' => 'required|exists:performer_roles,id',
            'is_active' => 'required|boolean',
            'phone' => 'required|string|max:100',
            'account_number' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'aktif';

        Performer::create($request->all());

        return redirect()->route('pengisi-acara.index')->with('success', 'Pengisi acara berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $performer = Performer::findOrFail($id);
        $roles = PerformerRole::all();
        return view('admin.pengisi-acara.edit', compact('performer', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $performer = Performer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:laki-laki,perempuan',
            'performer_role_id' => 'required|exists:performer_roles,id',
            'is_active' => 'required|boolean',
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

    public function destroy(string $id)
    {
        $performer = Performer::findOrFail($id);
        $performer->delete();

        return redirect()->route('pengisi-acara.index')->with('success', 'Pengisi acara berhasil dihapus.');
    }
}
