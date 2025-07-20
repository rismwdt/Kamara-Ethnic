<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Performer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'event_id',
        'date',
        'start_time',
        'end_time',
        'location_detail',
        'client_name',
        'male_parents',
        'female_parents',
        'phone',
        'email',
        'nuance',
        'image',
        'notes',
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function performers()
    {
        return $this->belongsToMany(Performer::class, 'booking_performers');
    }

    // public function getScheduleLocationAttribute()
    // {
    //     return $this->schedule ? $this->schedule->location : null;
    // }

    // public function getUserEmailAttribute()
    // {
    //     return $this->user ? $this->user->email : null;
    // }
}
