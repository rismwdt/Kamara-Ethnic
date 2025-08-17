<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerformerRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerformerRoleController extends Controller
{
    public function index()
    {
        $roles = PerformerRole::orderBy('name')->paginate(10);
        return view('admin.peran.index', compact('roles'));
    }

    // Tidak perlu create() & edit() karena form-nya inline di index.

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:performer_roles,name'],
        ]);

        PerformerRole::create($data);

        return redirect()->route('peran.index')->with('success', 'Peran berhasil ditambahkan.');
    }

    public function update(Request $request, PerformerRole $peran)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('performer_roles', 'name')->ignore($peran->id),
            ],
        ]);

        $peran->update($data);

        return redirect()->route('peran.index')->with('success', 'Peran berhasil diperbarui.');
    }

    public function destroy(PerformerRole $peran)
    {
        $peran->delete();

        return redirect()->route('peran.index')->with('success', 'Peran berhasil dihapus.');
    }
}
