<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPerformer extends Model
{
    protected $table = 'booking_performers';

    protected $fillable = [
        'booking_id', 'performer_id'
    ];
}
