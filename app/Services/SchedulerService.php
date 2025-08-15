<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Performer;
use App\Models\PerformerRequirement;
use Illuminate\Support\Facades\DB;

class SchedulerService
{
    public function __construct(private \App\Services\ScheduleValidator $validator) {}

    public function checkAvailabilityAndMaybeAssignToExisting(
        int $bookingId,
        int $eventId,
        string $date,
        string $startTime,
        string $endTime,
        ?string $location = null,          // ← boleh null
        ?float $latitude = null,
        ?float $longitude = null,
        bool $assign = false
    ): array {
        $booking = Booking::findOrFail($bookingId);

        // Draft nilai terbaru (tanpa save)
        $booking->event_id   = $eventId;
        $booking->date       = $date;
        $booking->start_time = $startTime;
        $booking->end_time   = $endTime;

        // ⬇️ Jangan timpa kalau kosong
        if ($location !== null && trim($location) !== '') {
            $booking->location_detail = $location;
        }
        if ($latitude !== null)  { $booking->latitude  = $latitude;  }
        if ($longitude !== null) { $booking->longitude = $longitude; }

        // Buang atribut 'location' kalau sempat terset sebelumnya
        if (array_key_exists('location', $booking->getAttributes())) {
            unset($booking->location);
        }

        // Requirements
        $requirements = PerformerRequirement::where('event_id', $eventId)->get();
        if ($requirements->isEmpty()) {
            return ['available' => false, 'reason' => 'Tidak ada requirement performer untuk event ini.'];
        }

        $chosen = [];
        $gaps   = [];

        foreach ($requirements as $req) {
            $need = (int) $req->quantity;
            if ($need < 1) continue;

            $performers = Performer::where('is_active', 1)
                ->where('performer_role_id', $req->performer_role_id)
                ->orderBy('experience', 'desc')
                ->get();

            $pickedThisRole = [];
            foreach ($performers as $p) {
                if (count($pickedThisRole) >= $need) break;
                if ($this->validator->isPerformerAvailable($p, $booking, $req)) {
                    $pickedThisRole[] = ['id' => $p->id, 'name' => $p->name];
                }
            }

            if (count($pickedThisRole) < $need) {
                $gaps[$req->performer_role_id] = [
                    'need'      => $need,
                    'available' => count($pickedThisRole),
                    'names'     => array_column($pickedThisRole, 'name'),
                ];
            }

            $chosen = array_merge($chosen, $pickedThisRole);
        }

        if (!empty($gaps)) {
            return [
                'available' => false,
                'reason'    => 'Kebutuhan performer belum terpenuhi.',
                'gaps'      => $gaps,
            ];
        }

        if (!$assign) {
            return [
                'available'      => true,
                'performer_name' => implode(', ', array_column($chosen, 'name')),
                'assigned'       => false,
            ];
        }

        DB::transaction(function () use ($booking, $chosen) {
            // (opsional) double safety
            if (array_key_exists('location', $booking->getAttributes())) {
                unset($booking->location);
            }
            $booking->performers()->syncWithoutDetaching(array_column($chosen, 'id'));
            $booking->status = 'diterima';
            $booking->save();
        });

        return [
            'available'      => true,
            'performer_name' => implode(', ', array_column($chosen, 'name')),
            'assigned'       => true,
            'booking_id'     => $booking->id,
        ];
    }

    public function checkAvailabilityAndMaybeAssign(
        int $eventId,
        string $date,
        string $startTime,
        string $endTime,
        ?string $location = null,          // ← boleh null
        ?float $latitude = null,
        ?float $longitude = null,
        bool $assign = false
    ): array {
        // Build atribut draft tanpa menimpa kosong
        $attrs = [
            'event_id'   => $eventId,
            'date'       => $date,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ];
        if ($location !== null && trim($location) !== '') { $attrs['location_detail'] = $location; }
        if ($latitude !== null)  { $attrs['latitude']  = $latitude;  }
        if ($longitude !== null) { $attrs['longitude'] = $longitude; }

        $bookingDraft = new Booking($attrs);

        $requirements = PerformerRequirement::where('event_id', $eventId)->get();
        if ($requirements->isEmpty()) {
            return ['available' => false, 'reason' => 'Tidak ada requirement performer untuk event ini.'];
        }

        $chosen = [];

        foreach ($requirements as $req) {
            $need = (int)$req->quantity;
            if ($need < 1) continue;

            $performers = Performer::where('is_active', 1)
                ->where('performer_role_id', $req->performer_role_id)
                ->orderBy('experience', 'desc')
                ->get();

            $pickedThisRole = [];
            foreach ($performers as $p) {
                if (count($pickedThisRole) >= $need) break;
                if ($this->validator->isPerformerAvailable($p, $bookingDraft, $req)) {
                    $pickedThisRole[] = ['id' => $p->id, 'name' => $p->name];
                }
            }

            if (count($pickedThisRole) < $need) {
                return [
                    'available' => false,
                    'reason'    => 'Performer yang sesuai requirement tidak cukup/tersedia.',
                ];
            }

            $chosen = array_merge($chosen, $pickedThisRole);
        }

        if (!$assign) {
            return [
                'available'      => true,
                'performer_name' => implode(', ', array_column($chosen, 'name')),
                'assigned'       => false,
            ];
        }

        $bookingSaved = null;
        DB::transaction(function () use (&$bookingSaved, $bookingDraft, $chosen) {
            $bookingSaved = Booking::create($bookingDraft->toArray() + ['status' => 'diterima']);
            $bookingSaved->performers()->syncWithoutDetaching(array_column($chosen, 'id'));
        });

        return [
            'available'      => true,
            'performer_name' => implode(', ', array_column($chosen, 'name')),
            'assigned'       => true,
            'booking_id'     => $bookingSaved?->id,
        ];
    }

    public function assignPerformers()
    {
        $results = [];

        $bookings = Booking::where('status', 'tertunda')
            ->orderBy('priority', 'desc')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        foreach ($bookings as $booking) {
            $requirements = PerformerRequirement::where('event_id', $booking->event_id)->get();
            $assigned = [];

            foreach ($requirements as $req) {
                $countAssigned = 0;

                $performers = Performer::where('is_active', 1)
                    ->where('performer_role_id', $req->performer_role_id)
                    ->orderBy('experience', 'desc')
                    ->get();

                foreach ($performers as $performer) {
                    if ($countAssigned >= $req->quantity) break;

                    if ($this->validator->isPerformerAvailable($performer, $booking, $req)) {
                        $assigned[] = $performer->id;
                        $countAssigned++;
                    }
                }
            }

            DB::transaction(function () use ($booking, $assigned) {
                if (count($assigned) > 0) {
                    $booking->performers()->syncWithoutDetaching($assigned);
                    $booking->status = 'diterima';
                } else {
                    $booking->status = 'ditolak';
                }
                $booking->save();
            });

            $results[] = [
                'booking_id' => $booking->id,
                'event_id'   => $booking->event_id,
                'assigned'   => $assigned,
                'status'     => $booking->status,
            ];
        }

        return $results;
    }

    public function validateSchedule(
        string $date,
        string $startTime,
        string $endTime,
        ?string $location,
        ?float $latitude,
        ?float $longitude,
        int $performerId
    ) {
        $attrs = [
            'date'       => $date,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ];
        if ($location !== null && trim($location) !== '') { $attrs['location_detail'] = $location; }
        if ($latitude !== null)  { $attrs['latitude']  = $latitude; }
        if ($longitude !== null) { $attrs['longitude'] = $longitude; }

        $booking   = new Booking($attrs);
        $performer = (object)['id' => $performerId];

        return $this->validator->getAvailabilityDetail($performer, $booking);
    }
}
