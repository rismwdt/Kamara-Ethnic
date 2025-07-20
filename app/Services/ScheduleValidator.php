<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Location;
use App\Models\LocationEstimate;

class ScheduleValidator
{
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
                        'to' => $bookingLocationAddress,
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
                $missingEstimates[] = [
                    'from' => '-',
                    'to' => $toLocation->full_address,
                    'from_id' => null,
                    'to_id' => $toLocation->id,
                    'performer' => $performer->name,
                    'reason' => 'tidak_ada_acara_sebelumnya'
                ];

                $anyEstimateToLocation = LocationEstimate::where('to_location_id', $toLocation->id)->exists();

                if (!$anyEstimateToLocation) {
                    $missingEstimates[] = [
                        'from' => '[lokasi manapun]',
                        'to' => $toLocation->full_address,
                        'from_id' => null,
                        'to_id' => $toLocation->id,
                        'performer' => null,
                        'reason' => 'estimasi_belum_ada'
                    ];
                }
            }
        }

        return [$missingEstimates, $showAddLocationModal, $alamatList];
    }

    public function checkLocationEstimates(Booking $booking, array $performerIds): array
    {
        $booking->load('performers');

        $booking->performers->each(function ($performer) use ($performerIds) {
            if (!in_array($performer->id, $performerIds)) {
                $booking->performers->forget($performer->id);
            }
        });

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

    public function isPerformerAvailable($performerId, $date, $start, $end)
    {
        return !Booking::where('date', $date)
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
}
