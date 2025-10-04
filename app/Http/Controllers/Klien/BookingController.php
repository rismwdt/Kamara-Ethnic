<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function myOrders(Request $request)
    {
        $bookings = Booking::with('event')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('klien.booking.my', compact('bookings'));
    }

    public function store(Request $request)
    {
        Log::info('[booking.store] incoming', $request->only([
            'event_type','event_id','date','start_time','end_time',
            'location_detail','client_name','phone','email','nuance'
        ]));

        $data = $request->validate([
            'event_type'      => ['required','in:pernikahan,khitan,gathering,grand_opening,lainnya'],
            'event_id'        => ['required','exists:events,id'],
            'date'            => ['required','date','after_or_equal:'.now()->addDays(3)->toDateString()],
            'start_time'      => ['required','date_format:H:i'],
            'end_time'        => ['required','date_format:H:i','after:start_time'],
            'location_detail' => ['required','string'],
            'client_name'     => ['required','string','max:100'],
            'male_parents'    => ['required_if:event_type,pernikahan','nullable','string','max:150'],
            'female_parents'  => ['required_if:event_type,pernikahan','nullable','string','max:150'],
            'event_name'      => ['required_if:event_type,khitan,gathering,grand_opening,lainnya','nullable','string','max:120'],
            'description'     => ['required_if:event_type,lainnya','nullable','string','max:500'],
            'phone'           => ['required','string','max:20'],
            'email'           => ['nullable','email'],
            'nuance'          => ['nullable','string','max:50'],
            'notes'           => ['nullable','string'],
            'location_photo'  => ['nullable','image','max:2048'],
            'image'           => ['required','image','max:4096'],
        ]);

        do {
            $bookingCode = 'BK' . now()->format('ymd') . Str::upper(Str::random(4));
        } while (Booking::where('booking_code', $bookingCode)->exists());

        $event = Event::findOrFail($data['event_id']);
        $price = (int) ($event->price ?? 0);
        $dp    = (int) round($price * 0.5);

        $locationPhotoPath = $request->hasFile('location_photo')
            ? $request->file('location_photo')->store('location_photos', 'public')
            : null;

        $imagePath = $request->file('image')->store('payment_proofs', 'public');

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
            'status'          => 'tertunda',
        ]);

        Log::info('[booking.store] saved', ['id' => $booking->id, 'code' => $booking->booking_code]);

        return redirect()
            ->route('booking.invoice', $booking)
            ->with('pesanan_berhasil', true);
    }

    public function invoice(Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);

        $booking->load(['event','user']);
        $price  = (int) $booking->price;
        $dp     = (int) $booking->dp;
        $remain = max($price - $dp, 0);

        return view('klien.booking.invoice', compact('booking','price','dp','remain'));
    }
}
