<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ScheduleOptimizer
{
    /**
     * Aturan untuk KLIEN:
     * - Validasi form di sini (termasuk H-3).
     * - Cek kapasitas harian.
     * - Cek overlap jam untuk event yang sama.
     * - TIDAK menolak karena jarak/lat-long.
     */
    public function checkClientAvailability(array $input): array
    {
        $minDate = now()->addDays(3)->toDateString();

        // 1) Validasi input (di service)
        $v = Validator::make($input, [
            'event_id'        => ['required','exists:events,id'],
            'date'            => ['required','date','after_or_equal:'.$minDate],
            'start_time'      => ['required','date_format:H:i'],
            'end_time'        => ['required','date_format:H:i','after:start_time'],
            // lokasi tetap wajib, tapi lat/long tidak dipakai sebagai penentu ketersediaan
            'location_detail' => ['required','string'],
            'latitude'        => ['nullable','numeric'],
            'longitude'       => ['nullable','numeric'],
        ]);

        if ($v->fails()) {
            // biar controller bisa balikin 422
            throw new ValidationException($v);
        }

        $data = $v->validated();

        // 2) Cek kapasitas harian (tanpa jarak)
        $maxEventsPerDay = 5; // kebijakan
        $countDay = Booking::whereDate('date', $data['date'])
            ->whereIn('status', ['tertunda','diterima'])
            ->count();

        if ($countDay >= $maxEventsPerDay) {
            return [
                'available' => false,
                'message'   => 'Kuota acara di tanggal tersebut sudah penuh.',
            ];
        }

        // 3) Cek overlap jam untuk event yang sama (tanpa jarak)
        $hasOverlap = Booking::where('event_id', $data['event_id'])
            ->whereDate('date', $data['date'])
            ->whereIn('status', ['tertunda','diterima'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time',   [$data['start_time'], $data['end_time']])
                  ->orWhere(function ($qq) use ($data) {
                      $qq->where('start_time','<=',$data['start_time'])
                         ->where('end_time','>=',$data['end_time']);
                  });
            })
            ->exists();

        if ($hasOverlap) {
            return [
                'available' => false,
                'message'   => 'Waktu yang dipilih berbenturan dengan pesanan lain.',
            ];
        }

        // 4) (Opsional) tambahkan aturan lain yang relevan untuk klien — tanpa jarak
        //    ... misalnya maximum durasi, blackout date, dll.

        return [
            'available' => true,
            'message'   => 'Tersedia.',
        ];
    }

    /**
     * (Opsional) Mode admin:
     * versi ini boleh menilai jarak/coverage/travel time, digunakan oleh tombol
     * “Cek & Tetapkan Performer” di admin.
     */
    public function checkAdminAvailability(array $input): array
    {
        // mirip di atas, tapi latitude/longitude bisa required
        // lalu tambahkan perhitungan jarak/coverage dsb sesuai kebutuhan admin
        // ...
        return [
            'available' => true,
            'message'   => 'Tersedia (admin).',
        ];
    }
}
