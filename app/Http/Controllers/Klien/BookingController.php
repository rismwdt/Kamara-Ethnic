<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('[booking.store] incoming', $request->only([
        'event_type','event_id','date','start_time','end_time',
        'location_detail','latitude','longitude','client_name','phone','email','price','dp'
    ]));

        $minDate = now()->addDays(3)->toDateString();

        $data = $request->validate([
    'event_type'      => ['required','in:pernikahan,khitan,gathering,grand_opening,lainnya'],
    'event_id'        => ['required','exists:events,id'],

    'date'            => ['required','date','after_or_equal:'.now()->addDays(3)->toDateString()],
    'start_time'      => ['required','date_format:H:i'],
    'end_time'        => ['required','date_format:H:i','after:start_time'],

    'location_detail' => ['required','string'],
    'latitude'        => ['nullable','numeric'],
    'longitude'       => ['nullable','numeric'],

    // selalu wajib diisi (semua tipe)
    'client_name'     => ['required','string','max:100'],

    // hanya untuk pernikahan
    'male_parents'    => ['required_if:event_type,pernikahan','nullable','string','max:150'],
    'female_parents'  => ['required_if:event_type,pernikahan','nullable','string','max:150'],

    // untuk khitan/gathering/grand_opening/lainnya
    'event_name'      => ['required_if:event_type,khitan,gathering,grand_opening,lainnya','nullable','string','max:120'],

    // untuk lainnya saja
    'description'     => ['required_if:event_type,lainnya','nullable','string','max:500'],

    'phone'           => ['required','string','max:20'],
    'email'           => ['nullable','email'],
    'nuance'          => ['nullable','string','max:50'],

    'notes'           => ['nullable','string'],

    'location_photo'  => ['nullable','image','max:2048'],
    'image'           => ['required','image','max:4096'],

    'price'           => ['nullable','integer','min:0'],
    'priority'        => ['nullable','in:normal,darurat'],
    'is_family'       => ['nullable','boolean'],
]);

        // Booking code unik
        do {
            $bookingCode = 'BK' . now()->format('ymd') . Str::upper(Str::random(4));
        } while (Booking::where('booking_code', $bookingCode)->exists());

        // Ambil harga paket dari server, hitung DP 50%
        $event = Event::findOrFail($data['event_id']);
        $price = (int) ($event->price ?? 0);
        $dp    = (int) round($price * 0.5);

        // Upload
        $locationPhotoPath = $request->hasFile('location_photo')
            ? $request->file('location_photo')->store('location_photos', 'public')
            : null;

        $imagePath = $request->file('image')->store('payment_proofs', 'public');

        // Simpan
        $booking = Booking::create([
            'booking_code'    => $bookingCode,
            'user_id'         => auth()->id(),
            'event_type'      => $data['event_type'],
            'event_id'        => $data['event_id'],
            'price'           => $price,
            'dp'              => $dp,
            'date'            => $data['date'],
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'],
            'location_detail' => $data['location_detail'],
            'latitude'        => $data['latitude'] ?? null,
            'longitude'       => $data['longitude'] ?? null,
            'client_name'     => $data['client_name'],
            'male_parents'    => $data['event_type']==='pernikahan' ? ($data['male_parents'] ?? null) : null,
            'female_parents'  => $data['event_type']==='pernikahan' ? ($data['female_parents'] ?? null) : null,
            'event_name'      => in_array($data['event_type'], ['khitan','gathering','grand_opening','lainnya']) ? ($data['event_name'] ?? null) : null,
            'phone'           => $data['phone'],
            'email'           => $data['email'] ?? null,
            'nuance'          => $data['nuance'] ?? null,
            'location_photo'  => $locationPhotoPath,
            'image'           => $imagePath,
            'description'     => $data['event_type']==='lainnya' ? ($data['description'] ?? null) : null,
            'notes'           => $data['notes'] ?? null,
            'priority'        => $data['priority'] ?? 'normal',
            'is_family'       => (bool) ($data['is_family'] ?? 0),
            'status'          => 'tertunda',
        ]);

        info('Berhasil simpan booking', $booking->toArray());

        return redirect('/')->with('pesanan_berhasil', true);
    }
}
