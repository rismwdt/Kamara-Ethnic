<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Dompdf\Dompdf;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            $end   = Carbon::parse($booking->end_time, 'Asia/Jakarta');
            $duration = $start->diffInMinutes($end, false);
        }

        return view('admin.pesanan.show', compact('booking','duration'));
    }

    public function cetakPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        $bookings = Booking::whereBetween('date', [$start_date, $end_date])
            ->orderBy('date')
            ->get();

        $html = view('admin.pesanan.laporan-pdf', compact('bookings','start_date','end_date'))->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename=\"laporan-pemesanan.pdf\"');
    }

    public function edit($id)
    {
        $booking = Booking::with('event','performers')->findOrFail($id);
        return view('admin.pesanan.edit', compact('booking'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // hanya prioritas + is_family
        $data = $request->validate([
            'priority'  => 'nullable|in:normal,darurat',
            'is_family' => 'nullable|boolean',
        ]);

        $dirty = false;

        if (array_key_exists('priority', $data) && $data['priority'] !== null) {
            $booking->priority = $data['priority'];
            $dirty = true;
        }

        // checkbox -> boolean 0/1
        $booking->is_family = $request->boolean('is_family');
        $dirty = true;

        if ($dirty) $booking->save();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil diperbarui.');
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
            ->orderByDesc('is_family') // keluarga dulu
            ->orderByRaw("FIELD(priority,'darurat','normal')") // darurat > normal
            ->orderBy('start_time')
            ->get();

        $rejected = [];

        return view('admin.pesanan.rekomendasi', compact('tanggal','availableDates','recommended','rejected'));
    }
}
