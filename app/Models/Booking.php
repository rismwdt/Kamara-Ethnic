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
        'user_id',
        'date',
        'start_time',
        'end_time',
        'location_detail',
        'latitude',
        'longitude',
        'client_name',
        'male_parents',
        'female_parents',
        'phone',
        'email',
        'nuance',
        'location_photo',
        'image',
        'notes',
        'priority',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
