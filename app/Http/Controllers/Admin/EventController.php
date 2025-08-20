<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::oldest()->paginate(10);
        return view('admin.paket-acara.index', compact('events'));
    }

    public function create()
    {
        return view('admin.paket-acara.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:siraman,upacara_adat,sisingaan,lainnya',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }
        Event::create($data);

        return redirect()->route('paket-acara.index')->with('success', 'Paket acara berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $event = Event::findOrFail($id);
        return view('admin.paket-acara.edit', compact('event'));
    }

    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:siraman,upacara_adat,lainnya',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('paket-acara.index')->with('success', 'Paket acara berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);

    if ($event->image && Storage::disk('public')->exists($event->image)) {
        Storage::disk('public')->delete($event->image);
    }

    $event->delete();

    return redirect()->route('paket-acara.index')->with('success', 'Paket acara berhasil dihapus.');
    }
}
