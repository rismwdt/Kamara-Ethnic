<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Performer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LocationEstimate;
use App\Services\BookingService;
use App\Services\ScheduleOptimizer;
use App\Services\ScheduleValidator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class PesananController extends Controller
{
    public function index()
    {
        $bookings = Booking::oldest()->paginate(10);
        return view('admin.pesanan.index', compact('bookings'));
    }

    public function show(Booking $pesanan)
    {
        $booking = $pesanan;
        $duration = null;
        if ($booking->start_time && $booking->end_time) {
            $start = Carbon::parse($booking->start_time, 'Asia/Jakarta');
            $end = Carbon::parse($booking->end_time, 'Asia/Jakarta');
            $duration = $start->diffInMinutes($end, false);
        }
        return view('admin.pesanan.show', [
            'booking' => $booking,
            'duration' => $duration,
        ]);
    }

    public function cetakPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $bookings = Booking::whereBetween('date', [$start_date, $end_date])->orderBy('date')->get();

        $html = view('admin.pesanan.laporan-pdf', compact('bookings', 'start_date', 'end_date'))->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="laporan-pemesanan.pdf"');
    }

    public function edit($id)
    {
        $booking = Booking::with('event', 'performers')->findOrFail($id);
        $categories = $this->getPerformersByCategory();
        $validator = new ScheduleValidator();
        [$missingEstimates, $showAddLocationModal, $alamatList] = $validator->validate($booking);
        Performer::whereNull('category')->update(['category' => 'penari']);
        Performer::where('category', '')->update(['category' => 'penari']);
        $rekomendasi = app(ScheduleValidator::class)->getPerformerRecommendations($booking);
        return view('admin.pesanan.edit', compact(
            'booking',
            'categories',
            'missingEstimates',
            'showAddLocationModal',
            'alamatList',
            'rekomendasi'
        ));
    }

    private function getPerformersByCategory()
    {
        return \App\Models\Performer::where('status', 'aktif')
            ->get()
            ->groupBy(function ($performer) {
                return trim(preg_replace('/\s*\(.*?\)/', '', $performer->category));
            })
            ->sortKeys();
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
        $allBookings = [];

        foreach ($performerIds as $performerId) {
    $performer = Performer::find($performerId);

    // 1. Cek bentrok jadwal (time conflict)
    if (!$validator->isPerformerAvailable($performerId, $booking->date, $booking->start_time, $booking->end_time, $booking->id)) {
        return back()->withErrors(['performer' => "Performer {$performer->name} sudah terlibat dalam acara lain."]);
    }

    // 2. Cek batas maksimal acara per performer
    if (!$validator->canPerformerTakeMoreEvents($performerId, $booking->date, $booking->id)) {
        $count = $validator->getEventCountForPerformer($performerId, $booking->date, $booking->id);
        return back()->withErrors(['performer' => "Performer {$performer->name} sudah memiliki $count acara. Maksimal 2."]);
    }

    // 3. Validasi estimasi lokasi dari acara sebelumnya
    if (!$validator->hasLocationEstimate($performer, $booking)) {
        return back()->withErrors([
            'estimasi' => "Estimasi lokasi belum tersedia antara lokasi sebelumnya dan lokasi acara ini untuk performer {$performer->name}. Tambahkan estimasi terlebih dahulu.",
        ]);
    }

    // 4. Ambil semua booking performer untuk validasi antar semua acara
    $bookings = $validator->getBookingsForPerformer($performerId, $booking->date, $booking->id);
    $allBookings = array_merge($allBookings, $bookings->all());
}

        $allBookings[] = $booking;

        [$missingEstimates, $showModal] = $validator->checkLocationEstimatesBetweenBookings($allBookings);

        foreach ($missingEstimates as $issue) {
            return back()->withErrors([
                'estimasi' => "Estimasi antar lokasi belum tersedia antara {$issue['from']} dan {$issue['to']}. Tambahkan estimasi terlebih dahulu.",
            ]);
        }

        $autoAccept = true;
    }

    $booking->update(['status' => $autoAccept ? 'diterima' : 'tertunda']);
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

    public function rekomendasiHariIni(Request $request)
    {
        $tanggal = $request->input('tanggal') ?? now()->toDateString();
        $availableDates = Booking::where('status', 'tertunda')
            ->selectRaw('DATE(date) as tanggal')
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal');
        $recommended = Booking::whereDate('date', $tanggal)
            ->where('status', 'tertunda')
            ->get();
        $rejected = [];
        return view('admin.pesanan.rekomendasi', compact(
            'tanggal', 'recommended', 'availableDates', 'rejected'
        ));
    }

}
