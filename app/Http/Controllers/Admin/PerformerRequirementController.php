<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\PerformerRole;
use App\Http\Controllers\Controller;
use App\Models\PerformerRequirement;

class PerformerRequirementController extends Controller
{
    public function index()
{
    $requirements = PerformerRequirement::with(['event','performerRole'])
        ->orderBy('event_id')
        ->get()
        ->groupBy('event_id');

    $perPage = 10;
    $page = request('page', 1);
    $slice = $requirements->slice(($page-1)*$perPage, $perPage);

    $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $slice, $requirements->count(), $perPage, $page,
        ['path'=>request()->url(),'query'=>request()->query()]
    );

    return view('admin.pengaturan-paket-acara.index', [
        'grouped' => $paginated
    ]);
}

    public function create()
    {
        $events = Event::all();
        $roles = PerformerRole::all();
        return view('admin.pengaturan-paket-acara.create', compact('events', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'performer_role_id.*' => 'required|exists:performer_roles,id',
            'quantity.*' => 'required|integer|min:1',
        ]);

        foreach ($request->performer_role_id as $index => $roleId) {
            PerformerRequirement::create([
                'event_id' => $request->event_id,
                'performer_role_id' => $roleId,
                'quantity' => $request->quantity[$index],
                'is_unique' => isset($request->is_unique[$index]) ? 1 : 0,
                'notes' => $request->notes[$index] ?? null,
            ]);
        }

        return redirect()->route('pengaturan-paket-acara.index')
                         ->with('success', 'pengaturan-paket acara berhasil ditambahkan.');
    }

public function edit($eventId)
{
    $event = Event::findOrFail($eventId);
    $events = Event::all();
    $roles  = PerformerRole::all();

    $performerRequirements = PerformerRequirement::where('event_id', $eventId)->get();

    return view('admin.pengaturan-paket-acara.edit', [
        'event' => $event,
        'events' => $events,
        'roles' => $roles,
        'performerRequirements' => $performerRequirements,
    ]);
}

public function update(Request $request, $event_id)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
        'performer_role_id.*' => 'required|exists:performer_roles,id',
        'quantity.*' => 'required|integer|min:1',
    ]);

    // Hapus semua data performer requirement lama untuk event ini
    PerformerRequirement::where('event_id', $event_id)->delete();

    // Simpan ulang semua data dari form
    foreach ($request->performer_role_id as $index => $roleId) {
        PerformerRequirement::create([
            'event_id' => $request->event_id,
            'performer_role_id' => $roleId,
            'quantity' => $request->quantity[$index],
            'is_unique' => isset($request->is_unique[$index]) ? 1 : 0,
            'notes' => $request->notes[$index] ?? null,
        ]);
    }

    return redirect()->route('pengaturan-paket-acara.index')
                    ->with('success', 'pengaturan-paket acara berhasil diperbarui.');
}

    public function destroy($id)
    {
        PerformerRequirement::findOrFail($id)->delete();
        return redirect()->route('pengaturan-paket-acara.index')
                         ->with('success', 'Kebutuhan Paket acara berhasil dihapus.');
    }

    public function destroyByEvent($eventId)
    {
        PerformerRequirement::where('event_id', $eventId)->delete();
        return redirect()->route('pengaturan-paket-acara.index')
                        ->with('success', 'Semua pengaturan pengisi acara untuk event ini telah dihapus.');
    }
}
