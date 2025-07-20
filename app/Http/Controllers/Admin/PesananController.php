<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\Location;
use App\Models\Performer;
use Illuminate\Http\Request;
use App\Models\LocationEstimate;
use App\Services\BookingService;
use App\Services\ScheduleValidator;
use App\Http\Controllers\Controller;

class PesananController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();
        return view('admin.pesanan.index', compact('bookings'));
    }

    public function show(Booking $pesanan)
    {
        return view('admin.pesanan.show', ['booking' => $pesanan]);
    }

    public function edit($id)
    {
        $booking = Booking::with('event', 'performers')->findOrFail($id);
        $categories = $this->getPerformersByCategory();
        $validator = new ScheduleValidator();

        [$missingEstimates, $showAddLocationModal, $alamatList] = $validator->validate($booking);

        return view('admin.pesanan.edit', compact(
            'booking',
            'categories',
            'missingEstimates',
            'showAddLocationModal',
            'alamatList'
        ));
    }

    private function getPerformersByCategory()
    {
        return \App\Models\Performer::all()->groupBy('category');

        return $performers->groupBy(function ($performer) {

        return trim(preg_replace('/\s*\(.*?\)/', '', $performer->category));
        });
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'performer_ids' => 'array|nullable',
        ]);

        $performerIds = $request->performer_ids ?? [];

        $autoAccept = false;

        if (!empty($performerIds)) {
            $validator = new ScheduleValidator();

            foreach ($performerIds as $performerId) {
                $isAvailable = $validator->isPerformerAvailable(
                    $performerId,
                    $booking->date,
                    $booking->start_time,
                    $booking->end_time
                );

                if (!$isAvailable) {
                    $performer = \App\Models\Performer::find($performerId);
                    return back()->withErrors("Performer {$performer->name} sudah terlibat dalam acara lain di waktu ini.");
                }
            }

            [$missingEstimates, $showAddLocationModal] = $validator->checkLocationEstimates($booking, $performerIds);

            if (empty($missingEstimates) && !$showAddLocationModal) {
                $autoAccept = true;
            }
        }

        $booking->update([
            'status' => $autoAccept ? 'diterima' : ($request->status ?? 'tertunda'),
        ]);

        $booking->performers()->sync($performerIds);

        return redirect()->route('pesanan.index')->with('success', $autoAccept
            ? 'Pesanan berhasil diperbarui dan diterima otomatis.'
            : 'Pesanan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
