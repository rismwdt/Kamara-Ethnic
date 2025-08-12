<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Location;
use App\Models\Performer;
use Illuminate\Support\Str;
use App\Models\LocationEstimate;

class ScheduleValidator
{
    protected $bookings;
    protected $estimates;

    public function __construct()
    {
        $this->bookings = Booking::whereIn('status', ['accepted', 'pending'])->get();
        $this->estimates = LocationEstimate::all();
    }

    public function validate(Booking $booking)
    {
        $missingEstimates = [];
        $showAddLocationModal = false;
        $locations = Location::all();
        $bookingLocationAddress = $booking->location_detail;
        $toLocation = $locations->first(fn($loc) => $loc->full_address === $bookingLocationAddress);

        $alamatList = Booking::whereNotNull('location_detail')
            ->select('location_detail')
            ->distinct()
            ->orderBy('location_detail')
            ->pluck('location_detail');

        if (!$toLocation) {
            $missingEstimates[] = [
                'from' => '-',
                'to' => $bookingLocationAddress,
                'from_id' => null,
                'to_id' => null,
                'performer' => null,
                'reason' => 'lokasi_belum_terdaftar'
            ];
            $showAddLocationModal = true;
            return [$missingEstimates, $showAddLocationModal, $alamatList];
        }

        foreach ($booking->performers as $performer) {
            $countBookingsToday = $performer->bookings()
                ->whereDate('bookings.date', $booking->date)
                ->count();

            if ($countBookingsToday >= 5) {
                $missingEstimates[] = [
                    'from' => '-',
                    'to' => $toLocation->full_address,
                    'from_id' => null,
                    'to_id' => $toLocation->id,
                    'performer' => $performer->name,
                    'reason' => 'melebihi_batas_per_hari',
                ];
                continue;
            }

            $previousBooking = $performer->bookings()
                ->whereDate('bookings.date', $booking->date)
                ->where('bookings.id', '<>', $booking->id)
                ->where('bookings.end_time', '<', $booking->start_time)
                ->orderByDesc('bookings.end_time')
                ->first();

            if ($previousBooking) {
                $fromLocation = $locations->first(fn($loc) => $loc->full_address === $previousBooking->location_detail);

                if (!$fromLocation) {
                    $missingEstimates[] = [
                        'from' => $previousBooking->location_detail,
                        'to' => $toLocation->full_address,
                        'from_id' => null,
                        'to_id' => $toLocation->id,
                        'performer' => $performer->name,
                        'reason' => 'lokasi_asal_belum_terdaftar'
                    ];
                    continue;
                }

                $estimate = LocationEstimate::where('from_location_id', $fromLocation->id)
                    ->where('to_location_id', $toLocation->id)
                    ->first();

                if (!$estimate) {
                    $missingEstimates[] = [
                        'from' => $fromLocation->full_address,
                        'to' => $toLocation->full_address,
                        'from_id' => $fromLocation->id,
                        'to_id' => $toLocation->id,
                        'performer' => $performer->name,
                        'reason' => 'estimasi_belum_ada'
                    ];
                    continue;
                }

                $gap = strtotime($booking->start_time) - strtotime($previousBooking->end_time);
                if ($gap < ($estimate->duration * 60)) {
                    $missingEstimates[] = [
                        'from' => $fromLocation->full_address,
                        'to' => $toLocation->full_address,
                        'from_id' => $fromLocation->id,
                        'to_id' => $toLocation->id,
                        'performer' => $performer->name,
                        'reason' => 'waktu_tidak_cukup'
                    ];
                }
            } else {
                $anyEstimateToLocation = LocationEstimate::where('to_location_id', $toLocation->id)->exists();

                $reason = !$anyEstimateToLocation
                    ? 'tidak_ada_acara_dan_estimasi'
                    : 'tidak_ada_acara_sebelumnya';

                $missingEstimates[] = [
                    'from' => '-',
                    'to' => $toLocation->full_address,
                    'from_id' => null,
                    'to_id' => $toLocation->id,
                    'performer' => $performer->name,
                    'reason' => $reason,
                ];
            }
        }

        return [$missingEstimates, $showAddLocationModal, $alamatList];
    }

    public function checkLocationEstimates(Booking $booking, array $performerIds): array
    {
        $booking->load('performers');

        $booking->setRelation('performers', $booking->performers->whereIn('id', $performerIds));

        [$missingEstimates, $showAddLocationModal, ] = $this->validate($booking);

        return [$missingEstimates, $showAddLocationModal];
    }

    public function checkScheduleConflicts(Booking $booking, array $performerIds): bool
    {
        foreach ($performerIds as $performerId) {
            $performer = \App\Models\Performer::find($performerId);

            $conflict = $performer->bookings()
                ->where('bookings.id', '!=', $booking->id)
                ->whereDate('bookings.date', $booking->date)
                ->where(function ($query) use ($booking) {
                    $query->whereBetween('start_time', [$booking->start_time, $booking->end_time])
                        ->orWhereBetween('end_time', [$booking->start_time, $booking->end_time])
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<=', $booking->start_time)
                            ->where('end_time', '>=', $booking->end_time);
                        });
                })->exists();

            if ($conflict) return false;
        }

        return true;
    }

    public function isPerformerAvailable($performerId, $date, $start, $end, $excludeBookingId = null)
    {
        return !Booking::where('date', $date)
        ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
        ->whereIn('status', ['tertunda', 'diterima'])
        ->whereHas('performers', function ($query) use ($performerId) {
            $query->where('performer_id', $performerId);
        })
        ->where(function ($query) use ($start, $end) {
            $query->whereBetween('start_time', [$start, $end])
                ->orWhereBetween('end_time', [$start, $end])
                ->orWhere(function ($q) use ($start, $end) {
                    $q->where('start_time', '<=', $start)
                    ->where('end_time', '>=', $end);
                });
        })
        ->exists();
    }

    public function getPerformerRecommendations(Booking $booking)
    {
        $validator = $this;
        $recommendations = [];

        $performersByRole = Performer::where('status', 'aktif')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->get()
            ->groupBy('category');

        foreach ($performersByRole as $category => $performers) {
            $recommendations[$category] = $performers
                ->filter(function ($performer) use ($booking, $validator) {
                    $available = $validator->isPerformerAvailable(
                        $performer->id,
                        $booking->date,
                        $booking->start_time,
                        $booking->end_time,
                        $booking->id
                    );

                    if (!$available) {
                        logger("Performer TIDAK tersedia: {$performer->name} [{$performer->category}]");
                        return false;
                    }

                    $hasEstimate = $validator->hasLocationEstimate($performer, $booking);
                    if (!$hasEstimate) {
                        logger("Performer TIDAK punya estimasi lokasi: {$performer->name} [{$performer->category}]");
                        return false;
                    }

                    logger("Performer LOLOS: {$performer->name} [{$performer->category}]");
                    return true;
                })
                ->sortBy(function ($performer) use ($booking, $validator) {
                    return $validator->estimateTravelTime($performer, $booking);
                })
                ->values();
        }

        return $recommendations;
    }


    public function hasLocationEstimate($performer, Booking $booking): bool
    {
        $locations = Location::all();

        $fromBooking = $performer->bookings()
            ->whereDate('bookings.date', $booking->date)
            ->where('bookings.id', '<>', $booking->id)
            ->where('end_time', '<', $booking->start_time)
            ->orderByDesc('end_time')
            ->first();

        if (!$fromBooking) {
            return true;
        }

        $fromLocation = $locations->first(function ($loc) use ($fromBooking) {
            return Str::of($loc->full_address)->lower()->trim()
                == Str::of($fromBooking->location_detail)->lower()->trim();
        });

        $toLocation = $locations->first(function ($loc) use ($booking) {
            return Str::of($loc->full_address)->lower()->trim()
                == Str::of($booking->location_detail)->lower()->trim();
        });

        if (!$fromLocation || !$toLocation) return false;

        return LocationEstimate::where('from_location_id', $fromLocation->id)
            ->where('to_location_id', $toLocation->id)
            ->exists()
            || LocationEstimate::where('from_location_id', $toLocation->id)
            ->where('to_location_id', $fromLocation->id)
            ->exists();
    }

    public function estimateTravelTime($performer, Booking $booking): int
    {
        $locations = Location::all();
        $fromBooking = $performer->bookings()
            ->whereDate('bookings.date', $booking->date)
            ->where('bookings.id', '<>', $booking->id)
            ->where('end_time', '<', $booking->start_time)
            ->orderByDesc('end_time')
            ->first();
        if (!$fromBooking) return PHP_INT_MAX;
        $fromLocation = $locations->first(fn($loc) => $loc->full_address === $fromBooking->location_detail);
        $toLocation = $locations->first(fn($loc) => $loc->full_address === $booking->location_detail);
        if (!$fromLocation || !$toLocation) return PHP_INT_MAX;
        $estimate = LocationEstimate::where('from_location_id', $fromLocation->id)
            ->where('to_location_id', $toLocation->id)
            ->first();
        return $estimate?->duration ?? PHP_INT_MAX;
    }

    public function hasPerformerConflict(Booking $booking): bool
    {
        foreach ($booking->performers as $performer) {
            $conflict = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->where('performer_id', $performer->id);
            })
            ->where('id', '!=', $booking->id)
            ->where('date', $booking->date)
            ->where(function ($q) use ($booking) {
                $q->whereBetween('start_time', [$booking->start_time, $booking->end_time])
                  ->orWhereBetween('end_time', [$booking->start_time, $booking->end_time])
                  ->orWhere(function ($q) use ($booking) {
                      $q->where('start_time', '<=', $booking->start_time)
                        ->where('end_time', '>=', $booking->end_time);
                  });
            })
            ->exists();

            if ($conflict) return true;
        }

        return false;
    }

    public function hasMissingLocationEstimate(Booking $booking): bool
    {
        $locations = Location::all();

        $toLocation = $locations->first(fn($loc) => $loc->full_address === $booking->location_detail);
        if (!$toLocation) {
            return true;
        }

        foreach ($booking->performers as $performer) {
            $previousBooking = $performer->bookings()
                ->whereDate('bookings.date', $booking->date)
                ->where('bookings.id', '<>', $booking->id)
                ->where('bookings.end_time', '<', $booking->start_time)
                ->orderByDesc('bookings.end_time')
                ->first();

            if ($previousBooking) {
                $fromLocation = $locations->first(fn($loc) => $loc->full_address === $previousBooking->location_detail);

                if (!$fromLocation) {
                    return true;
                }

                $estimate = LocationEstimate::where('from_location_id', $fromLocation->id)
                    ->where('to_location_id', $toLocation->id)
                    ->first();

                if (!$estimate) {
                    return true;
                }

                $gap = strtotime($booking->start_time) - strtotime($previousBooking->end_time);
                if ($gap < ($estimate->duration * 60)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canPerformerTakeMoreEvents($performerId, $date, $bookingId = null)
    {
        return Booking::whereDate('bookings.date', $date)
            ->whereHas('performers', fn($q) => $q->where('performers.id', $performerId))
            ->when($bookingId, fn($q) => $q->where('bookings.id', '!=', $bookingId))
            ->count() < 2;
    }

    public function getEventCountForPerformer($performerId, $date, $bookingId = null)
    {
        return Booking::whereDate('bookings.date', $date)
            ->whereHas('performers', fn($q) => $q->where('performers.id', $performerId))
            ->when($bookingId, fn($q) => $q->where('bookings.id', '!=', $bookingId))
            ->count();
    }

    public function getBookingsForPerformer($performerId, $date, $bookingId = null)
    {
        return Booking::whereDate('bookings.date', $date)
            ->whereHas('performers', fn($q) => $q->where('performers.id', $performerId))
            ->when($bookingId, fn($q) => $q->where('bookings.id', '!=', $bookingId))
            ->get();
    }


    public function checkLocationEstimatesBetweenBookings(array $bookings)
    {
        $missingEstimates = [];
        $showAddLocationModal = false;

        for ($i = 0; $i < count($bookings); $i++) {
            for ($j = $i + 1; $j < count($bookings); $j++) {
                $bookingA = $bookings[$i];
                $bookingB = $bookings[$j];

                if ($bookingA->location === $bookingB->location) {
                    continue;
                }

                $locationA = Location::where('full_address', $bookingA->location)->first();
                $locationB = Location::where('full_address', $bookingB->location)->first();

                if (!$locationA || !$locationB) {
                    $missingEstimates[] = [
                        'from' => $bookingA->location,
                        'to' => $bookingB->location,
                        'reason' => 'lokasi_belum_terdaftar',
                    ];
                    $showAddLocationModal = true;
                    continue;
                }

                $estimateAB = LocationEstimate::where('from_location_id', $locationA->id)
                    ->where('to_location_id', $locationB->id)
                    ->first();

                $estimateBA = LocationEstimate::where('from_location_id', $locationB->id)
                    ->where('to_location_id', $locationA->id)
                    ->first();

                if (!$estimateAB || !$estimateBA) {
                    $missingEstimates[] = [
                        'from' => $locationA->full_address,
                        'to' => $locationB->full_address,
                        'reason' => 'estimasi_belum_ada',
                    ];
                    $showAddLocationModal = true;
                }
            }
        }

        return [$missingEstimates, $showAddLocationModal];
    }

    public static function getAllBookingsOnDate($date)
    {
        return Booking::where('date', $date)->get();
    }

}
