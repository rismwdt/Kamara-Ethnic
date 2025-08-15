<?php

namespace App\Http\Controllers\Klien;

use App\Models\Booking;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $prefix = 'BK';
        $datePart = now()->format('ymd');
        $randomPart = Str::upper(Str::random(4));
        $bookingCode = $prefix . $datePart . $randomPart;

        if ($request->hasFile('location_photo')) {
            $locationPhotoPath = $request->file('location_photo')->store('location_photos', 'public');
        } else {
            $locationPhotoPath = null;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('image', 'public');
        }

        $booking = Booking::create([
            'booking_code'    => $bookingCode,
            'event_id'        => $request->event_id,
            'user_id'         => auth()->id(),
            'date'            => $request->date,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'location_detail' => $request->location_detail,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'client_name'     => $request->client_name,
            'male_parents'    => $request->male_parents,
            'female_parents'  => $request->female_parents,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'nuance'          => $request->nuance,
            'location_photo'  => $locationPhotoPath,
            'image'           => $imagePath,
            'notes'           => $request->notes,
            'priority'        => 'normal',
            'status'          => 'tertunda',
        ]);

        info('Berhasil simpan booking', $booking->toArray());

        return redirect('/')->with('pesanan_berhasil', true);

    }
}
