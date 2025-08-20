<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Performer;
use App\Models\PerformerRequirement;
use Illuminate\Support\Facades\DB;

class SchedulerService
{
    public function __construct(private \App\Services\ScheduleValidator $validator) {}

    /**
     * Cek ketersediaan & (opsional) assign ke booking yang sudah ada.
     */
    public function checkAvailabilityAndMaybeAssignToExisting(
        int $bookingId,
        int $eventId,
        string $date,
        string $startTime,
        string $endTime,
        ?string $location = null,
        ?float $latitude = null,
        ?float $longitude = null,
        bool $assign = false
    ): array {
        $booking = Booking::findOrFail($bookingId);

        $this->validator->ensureValidWindow($date, $startTime, $endTime);

        // Draft nilai terbaru (tanpa save)
        $booking->event_id   = $eventId;
        $booking->date       = $date;
        $booking->start_time = $startTime;
        $booking->end_time   = $endTime;

        if ($location !== null && trim($location) !== '') { $booking->location_detail = $location; }
        if ($latitude !== null)  { $booking->latitude  = $latitude;  }
        if ($longitude !== null) { $booking->longitude = $longitude; }

        // Ambil requirement per role
        $requirements = PerformerRequirement::where('event_id', $eventId)->get();
        if ($requirements->isEmpty()) {
            return ['available' => false, 'reason' => 'Tidak ada requirement performer untuk event ini.'];
        }

        $chosen = [];
        $gaps   = [];

        foreach ($requirements as $req) {
            $need = max(1, (int)$req->quantity);

            // Kandidat per role (internal dulu, lalu eksternal) + pemerataan beban harian
            $internal = Performer::query()
                ->schedulable()->internal()
                ->where('performer_role_id', $req->performer_role_id)
                ->withCount([
                    'bookings as tasks_today' => function ($q) use ($booking) {
                        $q->whereDate('date', $booking->date)
                          ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
                    }
                ])
                ->orderBy('tasks_today')
                ->orderBy('name')
                ->orderBy('id')
                ->get();

            $external = Performer::query()
                ->schedulable()->external()
                ->where('performer_role_id', $req->performer_role_id)
                ->withCount([
                    'bookings as tasks_today' => function ($q) use ($booking) {
                        $q->whereDate('date', $booking->date)
                          ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
                    }
                ])
                ->orderBy('tasks_today')
                ->orderBy('name')
                ->orderBy('id')
                ->get();

            $picked = $this->pickGreedyWithExternalModels(
                $internal->all(),
                $external->all(),
                $booking,
                $req,
                $need
            );

            if (count($picked) < $need) {
                $gaps[$req->performer_role_id] = [
                    'need'      => $need,
                    'available' => count($picked),
                    'names'     => array_map(fn($p) => $p->name, $picked),
                ];
            }

            $chosen = array_merge($chosen, $picked);
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
                'performer_name' => implode(', ', array_map(fn($p) => $p->name, $chosen)),
                'assigned'       => false,
            ];
        }

        // Simpan penugasan
        DB::transaction(function () use ($booking, $chosen) {
            $payload = [];
            foreach ($chosen as $p) {
                $payload[$p->id] = [
                    'is_external'         => $p->is_external ? 1 : 0,
                    'confirmation_status' => $p->is_external ? 'tertunda' : 'dikonfirmasi',
                    'agreed_rate'         => null, // belum dipakai
                ];
            }
            $booking->performers()->syncWithoutDetaching($payload);

            $hasExternal      = collect($chosen)->contains(fn($x) => (bool)$x->is_external);
            $booking->status  = $hasExternal ? 'tertunda' : 'diterima';
            $booking->save();
        });

        return [
            'available'       => true,
            'performer_name'  => implode(', ', array_map(fn($p) => $p->name, $chosen)),
            'assigned'        => true,
            'booking_id'      => $booking->id,
            'booking_status'  => $booking->status,
        ];
    }

    /**
     * Cek ketersediaan & (opsional) buat booking baru sekaligus assign.
     */
    public function checkAvailabilityAndMaybeAssign(
        int $eventId,
        string $date,
        string $startTime,
        string $endTime,
        ?string $location = null,
        ?float $latitude = null,
        ?float $longitude = null,
        bool $assign = false
    ): array {
        $this->validator->ensureValidWindow($date, $startTime, $endTime);

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
            $need = max(1, (int)$req->quantity);

            $internal = Performer::query()
                ->schedulable()->internal()
                ->where('performer_role_id', $req->performer_role_id)
                ->withCount([
                    'bookings as tasks_today' => function ($q) use ($bookingDraft) {
                        $q->whereDate('date', $bookingDraft->date)
                          ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
                    }
                ])
                ->orderBy('tasks_today')
                ->orderBy('name')
                ->orderBy('id')
                ->get();

            $external = Performer::query()
                ->schedulable()->external()
                ->where('performer_role_id', $req->performer_role_id)
                ->withCount([
                    'bookings as tasks_today' => function ($q) use ($bookingDraft) {
                        $q->whereDate('date', $bookingDraft->date)
                          ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
                    }
                ])
                ->orderBy('tasks_today')
                ->orderBy('name')
                ->orderBy('id')
                ->get();

            $picked = $this->pickGreedyWithExternalModels(
                $internal->all(),
                $external->all(),
                $bookingDraft,
                $req,
                $need
            );

            if (count($picked) < $need) {
                return [
                    'available' => false,
                    'reason'    => 'Performer yang sesuai requirement tidak cukup/tersedia.',
                ];
            }

            $chosen = array_merge($chosen, $picked);
        }

        if (!$assign) {
            return [
                'available'      => true,
                'performer_name' => implode(', ', array_map(fn($p) => $p->name, $chosen)),
                'assigned'       => false,
            ];
        }

        $bookingSaved = null;
        DB::transaction(function () use (&$bookingSaved, $bookingDraft, $chosen) {
            $bookingSaved = Booking::create($bookingDraft->toArray());

            $payload = [];
            foreach ($chosen as $p) {
                $payload[$p->id] = [
                    'is_external'         => $p->is_external ? 1 : 0,
                    'confirmation_status' => $p->is_external ? 'tertunda' : 'dikonfirmasi',
                    'agreed_rate'         => null, // belum dipakai
                ];
            }

            $bookingSaved->performers()->syncWithoutDetaching($payload);

            $hasExternal          = collect($chosen)->contains(fn($x) => (bool)$x->is_external);
            $bookingSaved->status = $hasExternal ? 'tertunda' : 'diterima';
            $bookingSaved->save();
        });

        return [
            'available'       => true,
            'performer_name'  => implode(', ', array_map(fn($p) => $p->name, $chosen)),
            'assigned'        => true,
            'booking_id'      => $bookingSaved?->id,
            'booking_status'  => $bookingSaved?->status,
        ];
    }

    /**
     * Greedy: internal dulu, lanjut eksternal.
     * NOTE: isPerformerAvailable() mencakup: daily cap, overlap, travel+buffer, (opsional) jarak keras.
     */
    private function pickGreedyWithExternalModels(
        array $internalCandidates,
        array $externalCandidates,
        $booking,
        $requirement,
        int $need
    ): array {
        $picked = [];

        foreach ($internalCandidates as $p) {
            if (count($picked) >= $need) break;
            if ($this->validator->isPerformerAvailable($p, $booking, $requirement)) {
                $picked[] = $p;
            }
        }

        if (count($picked) < $need) {
            foreach ($externalCandidates as $p) {
                if (count($picked) >= $need) break;
                if ($this->validator->isPerformerAvailable($p, $booking, $requirement)) {
                    $picked[] = $p;
                }
            }
        }

        return $picked;
    }

    /**
     * API validasi schedule (detail alasan).
     */
    public function validateSchedule(
        string $date,
        string $startTime,
        string $endTime,
        ?string $location,
        ?float $latitude,
        ?float $longitude,
        int $performerId
    ) {
        $this->validator->ensureValidWindow($date, $startTime, $endTime);

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

    /**
     * Batch assign (Optimasi/Greedy Dashboard).
     * Urutan: is_family DESC, priority (darurat > normal), date, start_time.
     */
    public function assignPerformers()
    {
        $results = [];

        $bookings = Booking::where('status', 'tertunda')
            ->orderByDesc('is_family')
            ->orderByRaw("FIELD(priority,'darurat','normal')")
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        foreach ($bookings as $booking) {
            $requirements = PerformerRequirement::where('event_id', $booking->event_id)->get();
            $chosen = [];

            foreach ($requirements as $req) {
                $need = max(1, (int)$req->quantity);

                $internal = Performer::query()
                    ->schedulable()->internal()
                    ->where('performer_role_id', $req->performer_role_id)
                    ->withCount([
                        'bookings as tasks_today' => function ($q) use ($booking) {
                            $q->whereDate('date', $booking->date)
                              ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
                        }
                    ])
                    ->orderBy('tasks_today')
                    ->orderBy('name')
                    ->orderBy('id')
                    ->get();

                $external = Performer::query()
                    ->schedulable()->external()
                    ->where('performer_role_id', $req->performer_role_id)
                    ->withCount([
                        'bookings as tasks_today' => function ($q) use ($booking) {
                            $q->whereDate('date', $booking->date)
                              ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
                        }
                    ])
                    ->orderBy('tasks_today')
                    ->orderBy('name')
                    ->orderBy('id')
                    ->get();

                $picked = $this->pickGreedyWithExternalModels(
                    $internal->all(),
                    $external->all(),
                    $booking,
                    $req,
                    $need
                );

                $chosen = array_merge($chosen, $picked);
            }

            DB::transaction(function () use ($booking, $chosen) {
                if (count($chosen) > 0) {
                    $payload = [];
                    foreach ($chosen as $p) {
                        $payload[$p->id] = [
                            'is_external'         => $p->is_external ? 1 : 0,
                            'confirmation_status' => $p->is_external ? 'tertunda' : 'dikonfirmasi',
                            'agreed_rate'         => null, // belum dipakai
                        ];
                    }
                    $booking->performers()->syncWithoutDetaching($payload);

                    $hasExternal     = collect($chosen)->contains(fn($x) => (bool)$x->is_external);
                    $booking->status = $hasExternal ? 'tertunda' : 'diterima';
                } else {
                    $booking->status = 'ditolak';
                }
                $booking->save();
            });

            $results[] = [
                'booking_id' => $booking->id,
                'event_id'   => $booking->event_id,
                'assigned'   => array_map(fn($p) => $p->id, $chosen),
                'status'     => $booking->status,
            ];
        }

        return $results;
    }
}
