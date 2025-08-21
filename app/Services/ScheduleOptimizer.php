<?php

namespace App\Services;

use App\Models\Booking;
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
     * Jika tidak tersedia, kembalikan 'suggestions' slot alternatif di hari yang sama.
     */
    public function checkClientAvailability(array $input): array
    {
        $minDate = now()->addDays(3)->toDateString();

        // 1) Validasi input (sesuai aturanmu)
        $v = Validator::make($input, [
            'event_id'        => ['required','exists:events,id'],
            'date'            => ['required','date','after_or_equal:'.$minDate],
            'start_time'      => ['required','date_format:H:i'],
            'end_time'        => ['required','date_format:H:i','after:start_time'],
            'location_detail' => ['required','string'],
            'latitude'        => ['nullable','numeric'],
            'longitude'       => ['nullable','numeric'],
        ]);

        if ($v->fails()) {
            throw new ValidationException($v);
        }

        $data = $v->validated();

        // 2) Cek kapasitas harian (tanpa jarak)
        $maxEventsPerDay = 5; // kebijakan harian
        $countDay = Booking::whereDate('date', $data['date'])
            ->whereIn('status', ['tertunda','diterima'])
            ->count();

        if ($countDay >= $maxEventsPerDay) {
            return [
                'available'   => false,
                'reason'      => 'Kuota acara di tanggal tersebut sudah penuh.',
                'suggestions' => $this->suggestSameDaySlotsForClient(
                    (int)$data['event_id'],
                    $data['date'],
                    $data['start_time'],
                    $data['end_time'],
                    30, '06:00', '22:00', 10
                ),
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
                'available'   => false,
                'reason'      => 'Waktu yang dipilih berbenturan dengan pesanan lain.',
                'suggestions' => $this->suggestSameDaySlotsForClient(
                    (int)$data['event_id'],
                    $data['date'],
                    $data['start_time'],
                    $data['end_time'],
                    30, '06:00', '22:00', 10
                ),
            ];
        }

        // 4) Tidak ada aturan lain ditambahkan
        return [
            'available' => true,
            'message'   => 'Tersedia.',
        ];
    }

    /**
     * Saran slot tersedia di HARI YANG SAMA (untuk klien).
     * Hanya menghindari overlap & menjaga kapasitas harian — tidak menilai jarak.
     * Ini BUKAN validasi; hanya helper untuk UX.
     */
    private function suggestSameDaySlotsForClient(
        int $eventId,
        string $date,
        string $startTime,
        string $endTime,
        int $stepMinutes = 30,
        string $openAt = '06:00',
        string $closeAt = '22:00',
        int $limit = 10
    ): array {
        $durationMin = max(15, (int) round((strtotime($endTime) - strtotime($startTime)) / 60));
        if ($durationMin <= 0) return [];

        $openTs    = strtotime("$date $openAt");
        $closeTs   = strtotime("$date $closeAt");
        $endLatest = $closeTs - ($durationMin * 60);

        $suggestions = [];

        // daftar booking event yang sama pada hari itu (untuk cek overlap ringan)
        $bookings = Booking::where('event_id', $eventId)
            ->whereDate('date', $date)
            ->whereIn('status', ['tertunda','diterima'])
            ->get(['start_time','end_time']);

        // kapasitas harian yang sedang terpakai
        $maxEventsPerDay = 5;
        $countDay = Booking::whereDate('date', $date)
            ->whereIn('status', ['tertunda','diterima'])
            ->count();

        for ($startTs = $openTs; $startTs <= $endLatest; $startTs += $stepMinutes * 60) {
            $candStart = date('H:i', $startTs);
            $candEnd   = date('H:i', $startTs + $durationMin * 60);

            // overlap sederhana
            $overlap = $bookings->contains(function ($b) use ($candStart, $candEnd) {
                return !(($b->end_time <= $candStart) || ($b->start_time >= $candEnd));
            });
            if ($overlap) continue;

            // kapasitas harian jika slot ini diterima
            if ($countDay + 1 > $maxEventsPerDay) continue;

            $suggestions[] = [
                'start' => $candStart,
                'end'   => $candEnd,
                'label' => $candStart . ' - ' . $candEnd,
            ];
            if (count($suggestions) >= $limit) break;
        }

        return $suggestions;
    }
}
