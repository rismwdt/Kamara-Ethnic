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
     * - Internal didahulukan, lalu eksternal (fallback)
     * - Tulis pivot: is_external, confirmation_status (IDN), agreed_rate (null)
     * - Kebijakan status: jika ada eksternal -> booking 'tertunda', selain itu 'diterima'
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

        // Validasi window waktu (tambahkan method ensureValidWindow di ScheduleValidator jika belum ada)
        $this->validator->ensureValidWindow($date, $startTime, $endTime);

        // Draft nilai terbaru (tanpa save)
        $booking->event_id   = $eventId;
        $booking->date       = $date;
        $booking->start_time = $startTime;
        $booking->end_time   = $endTime;

        if ($location !== null && trim($location) !== '') {
            $booking->location_detail = $location;
        }
        if ($latitude !== null)  { $booking->latitude  = $latitude;  }
        if ($longitude !== null) { $booking->longitude = $longitude; }

        // Hindari kolom 'location' yang tidak dipakai (kalau ada)
        if (array_key_exists('location', $booking->getAttributes())) {
            unset($booking->location);
        }

        // Ambil requirement per role untuk event ini
        $requirements = PerformerRequirement::where('event_id', $eventId)->get();
        if ($requirements->isEmpty()) {
            return ['available' => false, 'reason' => 'Tidak ada requirement performer untuk event ini.'];
        }

        $chosen = [];   // array of Performer models terpilih (lintas role)
        $gaps   = [];   // role yang tidak terpenuhi

        foreach ($requirements as $req) {
            $need = max(1, (int)$req->quantity);

            // Kandidat internal & eksternal utk role terkait
            $internal = Performer::query()
                ->schedulable()->internal()
                ->where('performer_role_id', $req->performer_role_id)
                ->orderBy('name') // stabil
                ->get();

            $external = Performer::query()
                ->schedulable()->external()
                ->where('performer_role_id', $req->performer_role_id)
                ->orderBy('name')
                ->get();

            // Greedy: internal dulu, lalu eksternal
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

        // Tulis ke pivot + update status booking
        DB::transaction(function () use ($booking, $chosen) {
            // safety
            if (array_key_exists('location', $booking->getAttributes())) {
                unset($booking->location);
            }

            // siapkan payload pivot per performer
            $payload = [];
            foreach ($chosen as $p) {
                $payload[$p->id] = [
                    'is_external'         => $p->is_external ? 1 : 0,
                    'confirmation_status' => $p->is_external ? 'tertunda' : 'dikonfirmasi',
                    'agreed_rate'         => null,
                ];
            }

            $booking->performers()->syncWithoutDetaching($payload);

            // Kebijakan status booking
            $hasExternal      = collect($chosen)->contains(fn($x) => (bool)$x->is_external);
            $booking->status  = $hasExternal ? 'tertunda' : 'diterima';
            $booking->save();
        });

        return [
            'available'      => true,
            'performer_name' => implode(', ', array_map(fn($p) => $p->name, $chosen)),
            'assigned'       => true,
            'booking_id'     => $booking->id,
            'booking_status'  => $booking->status
        ];
    }

    /**
     * Cek ketersediaan & (opsional) buat booking baru + assign.
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
        // Validasi window waktu
        $this->validator->ensureValidWindow($date, $startTime, $endTime);

        // Build booking draft
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
                ->orderBy('name')
                ->get();

            $external = Performer::query()
                ->schedulable()->external()
                ->where('performer_role_id', $req->performer_role_id)
                ->orderBy('name')
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
            // Simpan booking dulu tanpa status, nanti ditentukan dari komposisi internal/eksternal
            $bookingSaved = Booking::create($bookingDraft->toArray());

            $payload = [];
            foreach ($chosen as $p) {
                $payload[$p->id] = [
                    'is_external'         => $p->is_external ? 1 : 0,
                    'confirmation_status' => $p->is_external ? 'tertunda' : 'dikonfirmasi',
                    'agreed_rate'         => null,
                ];
            }

            $bookingSaved->performers()->syncWithoutDetaching($payload);

            $hasExternal          = collect($chosen)->contains(fn($x) => (bool)$x->is_external);
            $bookingSaved->status = $hasExternal ? 'tertunda' : 'diterima';
            $bookingSaved->save();
        });

        return [
            'available'      => true,
            'performer_name' => implode(', ', array_map(fn($p) => $p->name, $chosen)),
            'assigned'       => true,
            'booking_id'     => $bookingSaved?->id,
            'booking_status'  => $bookingSaved?->status,
        ];
    }

    /**
     * Batch assign untuk booking status 'tertunda' dengan urutan:
     * - is_family DESC (keluarga dulu)
     * - priority: darurat > normal
     * - tanggal, lalu jam mulai
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
                    ->orderBy('name')
                    ->get();

                $external = Performer::query()
                    ->schedulable()->external()
                    ->where('performer_role_id', $req->performer_role_id)
                    ->orderBy('name')
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
                            'agreed_rate'         => null,
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

    // ===================== helper greedy =====================

    /**
     * Pilih performer: internal dulu, lalu eksternal.
     * Memakai validator.isPerformerAvailable (yang sebaiknya memfilter jadwal pivot 'tertunda'/'dikonfirmasi').
     */
    private function pickGreedyWithExternalModels(
        array $internalCandidates,
        array $externalCandidates,
        $booking,
        $requirement,
        int $need
    ): array {
        $picked = [];

        // internal dulu
        foreach ($internalCandidates as $p) {
            if (count($picked) >= $need) break;
            if ($this->validator->isPerformerAvailable($p, $booking, $requirement)) {
                $picked[] = $p;
            }
        }

        // eksternal fallback
        if (count($picked) < $need) {
            foreach ($externalCandidates as $p) {
                if (count($picked) >= $need) break;

                // jika tidak melacak jadwal eksternal, bisa set $available = true;
                $available = $this->validator->isPerformerAvailable($p, $booking, $requirement);

                if ($available) {
                    $picked[] = $p;
                }
            }
        }

        return $picked;
    }
}
