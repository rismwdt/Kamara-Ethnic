<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'event_type',
        'event_id',
        'user_id',
        'price',
        'dp',
        'date',
        'start_time',
        'end_time',
        'location_detail',
        'latitude',
        'longitude',
        'client_name',
        'event_name',
        'male_parents',
        'female_parents',
        'phone',
        'email',
        'nuance',
        'location_photo',
        'image',
        'description',
        'notes',
        'priority',
        'is_family',
        'status',
    ];

    protected $casts = [
        'price'     => 'integer',
        'dp'        => 'integer',
        'date'      => 'date',
        'latitude'  => 'float',
        'longitude' => 'float',
        'is_family' => 'boolean',
    ];

    // RELATIONS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function performers(): BelongsToMany
    {
        return $this->belongsToMany(Performer::class, 'booking_performers')
            ->withPivot(['is_external', 'confirmation_status', 'agreed_rate'])
            ->withTimestamps();
    }

    // SCOPES
    public function scopeStatus($q, string $status)
    {
        return $q->where('status', $status);
    }

    public function scopeOnDate($q, $date)
    {
        return $q->whereDate('date', Carbon::parse($date)->toDateString());
    }

    public function scopeBetweenDates($q, $start, $end)
    {
        return $q->whereBetween('date', [
            Carbon::parse($start)->toDateString(),
            Carbon::parse($end)->toDateString(),
        ]);
    }

    public function scopeToday($q)
    {
        return $q->onDate(today());
    }

    public function scopeUpcoming($q)
    {
        return $q->where('date', '>=', today()->toDateString());
    }

    public function scopeEvent($q, int $eventId)
    {
        return $q->where('event_id', $eventId);
    }

    // HELPERS
    public function startAt(): ?Carbon
    {
        if (!$this->date || !$this->start_time) {
            return null;
        }
        return Carbon::parse(sprintf('%s %s', $this->date->toDateString(), $this->start_time));
    }

    public function endAt(): ?Carbon
    {
        if (!$this->date || !$this->end_time) {
            return null;
        }
        return Carbon::parse(sprintf('%s %s', $this->date->toDateString(), $this->end_time));
    }

    public function durationMinutes(): ?int
    {
        $s = $this->startAt();
        $e = $this->endAt();
        return ($s && $e) ? $s->diffInMinutes($e, false) : null;
    }
}
