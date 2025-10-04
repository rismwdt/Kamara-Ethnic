<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Performer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class PesananController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['event','performers'])
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        $performers = Performer::leftJoin('performer_roles as pr', 'pr.id', '=', 'performers.performer_role_id')
            ->orderBy('pr.name')
            ->orderBy('performers.name')
            ->get([
                'performers.id',
                'performers.name',
                'performers.is_external',
                'performers.performer_role_id',
                DB::raw('pr.name as role_name'),
            ]);
        $performersByRole = $performers->groupBy(fn($p) => $p->role_name ?: 'Tanpa Peran');

        $dates = $bookings->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        $busyRows = DB::table('booking_performers as bp')
            ->join('bookings as b', 'b.id', '=', 'bp.booking_id')
            ->whereIn('b.date', $dates)
            ->select('b.date', 'bp.performer_id')
            ->get();

        $busyByDate = $busyRows->groupBy('date')
            ->map(fn($rows) => $rows->pluck('performer_id')->unique()->values()->all())
            ->toArray();

        return view('admin.pesanan.index', compact('bookings','performersByRole','busyByDate'));
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

    public function create()
    {
        $events = Event::orderBy('name')->get(['id','name','price']);
        return view('admin.pesanan.create', compact('events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id'        => ['required','exists:events,id'],
            'event_type'      => ['required','in:pernikahan,khitan,gathering,grand_opening,lainnya'],
            'client_name'     => ['required','string','max:255'],
            'female_parents'  => ['nullable','string','max:255'],
            'male_parents'    => ['nullable','string','max:255'],
            'event_name'      => ['nullable','string','max:255'],
            'description'     => ['nullable','string','max:1000'],
            'phone'           => ['required','string','max:50'],
            'email'           => ['required','email','max:255'],
            'date'            => ['required','date'],
            'start_time'      => ['required','date_format:H:i'],
            'end_time'        => ['required','date_format:H:i','after:start_time'],
            'nuance'          => ['required','string','max:255'],
            'location_detail' => ['required','string','max:1000'],
            'price'           => ['nullable','numeric','min:0'],
            'dp'              => ['nullable','numeric','min:0'],
            'location_photo'  => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'image'           => ['required','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'notes'           => ['nullable','string','max:1000'],
        ]);

        $locationPhotoPath = $request->file('location_photo')
            ? $request->file('location_photo')->store('public/pesanan/lokasi') : null;
        $dpProofPath = $request->file('image')->store('public/pesanan/dp');

        $booking = new Booking();
        $booking->fill([
            'event_id'        => $data['event_id'],
            'event_type'      => $data['event_type'],
            'client_name'     => $data['client_name'],
            'female_parents'  => $data['female_parents'] ?? null,
            'male_parents'    => $data['male_parents'] ?? null,
            'event_name'      => $data['event_name'] ?? null,
            'description'     => $data['description'] ?? null,
            'phone'           => $data['phone'],
            'email'           => $data['email'],
            'date'            => $data['date'],
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'],
            'nuance'          => $data['nuance'],
            'location_detail' => $data['location_detail'],
            'price'           => $data['price'] ?? 0,
            'dp'              => $data['dp'] ?? 0,
            'priority'        => 'normal',
            'is_family'       => false,
            'status'          => 'tertunda',
            'notes'           => $data['notes'] ?? null,
        ]);

        if ($locationPhotoPath) $booking->location_photo = str_replace('public/','storage/',$locationPhotoPath);
        $booking->dp_image = str_replace('public/','storage/',$dpProofPath);

        $booking->save();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dibuat.');
    }

    public function cetakPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start_date = Carbon::parse($request->start_date)->toDateString();
        $end_date   = Carbon::parse($request->end_date)->toDateString();

        $bookings = Booking::with('event')
            ->whereBetween('date', [$start_date, $end_date])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $html = view('admin.pesanan.laporan-pdf', compact('bookings','start_date','end_date'))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = sprintf('laporan-pemesanan_%s_sampai_%s.pdf', $start_date, $end_date);
        return $dompdf->stream($filename, ['Attachment' => false]);
    }

    public function edit($id)
    {
        $booking = Booking::with('event','performers')->findOrFail($id);
        return view('admin.pesanan.edit', compact('booking'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $data = $request->validate([
            'priority'  => 'nullable|in:normal,darurat',
            'is_family' => 'nullable|boolean',
        ]);

        $dirty = false;

        if (array_key_exists('priority', $data) && $data['priority'] !== null) {
            $booking->priority = $data['priority'];
            $dirty = true;
        }

        $booking->is_family = $request->boolean('is_family');
        $dirty = true;

        if ($dirty) $booking->save();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        // if ($booking->dp_image) Storage::delete(str_replace('storage/','public/',$booking->dp_image));
        // if ($booking->location_photo) Storage::delete(str_replace('storage/','public/',$booking->location_photo));
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
            ->orderByDesc('is_family')
            ->orderByRaw("FIELD(priority,'darurat','normal')")
            ->orderBy('start_time')
            ->get();

        $rejected = [];

        return view('admin.pesanan.rekomendasi', compact('tanggal','availableDates','recommended','rejected'));
    }

    public function tambahPengisiAcara(Request $r)
    {
        $query = Performer::leftJoin('performer_roles as pr', 'pr.id', '=', 'performers.performer_role_id')
            ->orderBy('pr.name')
            ->orderBy('performers.name')
            ->get([
                'performers.id',
                'performers.name',
                'performers.is_external',
                'performers.performer_role_id',
                \DB::raw('pr.name as role_name'),
            ]);

        $candidates = $query->map(function ($p) {
            return [
                'id'         => $p->id,
                'name'       => $p->name,
                'role_name'  => $p->role_name,
                'is_external'=> (bool)$p->is_external,
                'available'  => true,
            ];
        });

        if ($r->ajax() || $r->wantsJson() || $r->expectsJson()) {
            return response()->json($candidates);
        }

        return view('admin.pesanan.tambah-pengisi-acara', [
            'candidates' => $candidates
        ]);
    }

    public function simpanPengisiAcaraManual(Request $r)
    {
        $data = $r->validate([
            'booking_id'      => ['required','exists:bookings,id'],
            'performer_ids'   => ['required','array','min:1'],
            'performer_ids.*' => ['exists:performers,id'],
            'note'            => ['nullable','string','max:500'],
        ]);

        $booking = Booking::findOrFail($data['booking_id']);

        $booking->performers()->syncWithoutDetaching($data['performer_ids']);

        $booking->status = 'diterima';
        $booking->save();

        $assigned = \App\Models\Performer::with('role')
            ->whereIn('id', $data['performer_ids'])
            ->get()
            ->map(fn($p) => [
                'id'        => $p->id,
                'name'      => $p->name,
                'role_id'   => $p->performer_role_id,
                'role_name' => optional($p->role)->name,
            ])->values();

        return response()->json([
            'success'        => true,
            'assigned'       => $assigned,
            'booking_status' => $booking->status,   
        ]);
    }
}
