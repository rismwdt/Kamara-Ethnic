<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'type',
        'status'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
