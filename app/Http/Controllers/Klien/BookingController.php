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
        // info('Masuk ke controller');
        // info($request->all());

        // Simpan bukti transfer ke storage
        $imagePath = null;
        if ($request->hasFile('bukti_tf')) {
            $imagePath = $request->file('bukti_tf')->store('bukti_tf', 'public');
        }

        // Simpan data ke database
        $booking = Booking::create([
            'booking_code'    => Str::random(10),
            'event_id'        => $request->event_id,
            'user_id'         => auth()->id(),
            'date'            => $request->tanggal,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'location_detail'        => $request->alamat,
            'client_name'     => $request->nama_pengantin,
            'male_parents'    => $request->ortu_pria,
            'female_parents'  => $request->ortu_wanita,
            'phone'           => $request->no_hp,
            'email'           => $request->email,
            'nuance'          => $request->tema,
            'image'           => $imagePath,
            'notes'           => $request->catatan,
            'status'          => 'tertunda',
        ]);

        // Untuk debug
        info('Berhasil simpan booking', $booking->toArray());

        return redirect('/')->with('pesanan_berhasil', true);

    }
}
