<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Booking;

class ScheduleOptimizer
{
    /**
     * Mengecek ketersediaan jadwal berbasis data pada tabel bookings.
     *
     * @param  string      $date        Format 'Y-m-d'
     * @param  string      $startTime   Format 'H:i' atau 'H:i:s'
     * @param  string      $endTime     Format 'H:i' atau 'H:i:s'
     * @param  float|null  $latitude
     * @param  float|null  $longitude
     * @return array{available: bool, message: string}
     */
    public function checkScheduleAvailability(
        $date,
        $startTime,
        $endTime,
        $latitude = null,
        $longitude = null,
    ) {
        // Normalisasi ke 'H:i:s' supaya perbandingan waktu konsisten
        $startTime = $this->normalizeTime($startTime);
        $endTime   = $this->normalizeTime($endTime);

        // 1) Batas maksimal acara per hari (opsional, sesuaikan)
        $maxEventsPerDay = 5;
        $eventCount = Booking::query()
            ->whereDate('date', $date)
            ->whereIn('status', ['tertunda', 'diterima'])
            ->count();

        if ($eventCount >= $maxEventsPerDay) {
            return ['available' => false, 'message' => 'Kuota acara di hari tersebut sudah penuh.'];
        }

        // 2) Cek bentrok waktu pada tanggal yang sama dari tabel bookings
        // Aturan overlap:
        // request.start < existing.end  AND  request.end > existing.start
        $conflictingBookings = Booking::query()
            ->whereDate('date', $date)
            ->whereIn('status', ['tertunda', 'diterima'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time',   '>', $startTime);
            })
            ->get(['start_time', 'end_time', 'latitude', 'longitude']);

        if ($conflictingBookings->isEmpty()) {
            return ['available' => true, 'message' => 'Jadwal tersedia.'];
        }

        // 3) (Opsional) Periksa waktu tempuh antar lokasi
        //    Bila salah satu titik tidak punya koordinat, kita lewati cek jarak dan tetap
        //    menganggap bentrok jika interval waktu overlap.
        $requestStart = Carbon::createFromFormat('H:i:s', $startTime);

        foreach ($conflictingBookings as $bk) {
            // Jika tidak punya koordinat lengkap, cukup anggap bentrok karena waktu overlap
            if ($latitude === null || $longitude === null || $bk->latitude === null || $bk->longitude === null) {
                return [
                    'available' => false,
                    'message'   => 'Waktu acara bertabrakan dengan pesanan lain pada tanggal yang sama.',
                ];
            }

            // Hitung jarak (km) menggunakan Haversine
            $distKm = $this->haversineKm($latitude, $longitude, (float)$bk->latitude, (float)$bk->longitude);

            // Estimasi waktu tempuh (kecepatan 40 km/jam + buffer 15 menit)
            $travelTimeMinutes = ($distKm / 40) * 60 + 15;

            $existingEnd = $this->toCarbonTime($bk->end_time);
            $diffMinutes = $existingEnd->diffInMinutes($requestStart, false);  // bisa negatif

            if ($diffMinutes < $travelTimeMinutes) {
                return [
                    'available' => false,
                    'message'   => 'Waktu tempuh (dengan buffer) tidak cukup dari lokasi acara sebelumnya.',
                ];
            }
        }

        // Lolos semua cek
        return ['available' => true, 'message' => 'Jadwal tersedia.'];
    }

    private function normalizeTime(string $time): string
    {
        // Terima 'H:i' atau 'H:i:s' → kembalikan 'H:i:s'
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return $time;
        }
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time . ':00';
        }
        // fallback: parse fleksibel
        return Carbon::parse($time)->format('H:i:s');
    }

    private function toCarbonTime(string $time): Carbon
    {
        $t = $this->normalizeTime($time);
        return Carbon::createFromFormat('H:i:s', $t);
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
