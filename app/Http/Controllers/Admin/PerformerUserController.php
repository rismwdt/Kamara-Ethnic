<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Performer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerformerUserController extends Controller
{
    public function index()
    {
        $performers = User::role('performer')->paginate(10);
        return view('admin.akun.index', compact('performers'));
    }

    public function create()
    {
        $performers = Performer::where('is_external', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.akun.create', compact('performers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'performer_id' => [
                'required',
                Rule::exists('performers','id')->where(fn($q) => $q->where('is_external', 0)),
            ],
            'email'    => ['required','email','unique:users,email'],
            'password' => ['required','string','min:6'],
        ]);

        $perf = Performer::findOrFail($data['performer_id']);

        $user = User::create([
            'name'     => $perf->name,
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('performer');

        return redirect()->route('akun.index')
            ->with('success','Akun berhasil ditambahkan.');
    }

    public function edit(User $akun)
        {
            return view('admin.akun.edit', ['user' => $akun]);
        }

        public function update(Request $request, User $akun)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:100'],
            'email' => ['required','email', Rule::unique('users','email')->ignore($akun->id)],
        ]);

        $akun->update($data);

        return redirect()->route('akun.edit', $akun)
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request, User $akun)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $akun->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('akun.edit', $akun)
            ->with('success', 'Password berhasil diubah.');
    }

    public function destroy(User $akun)
    {
        $akun->delete();
        return redirect()->route('akun.index')->with('success','Akun berhasil dihapus.');
    }
}
