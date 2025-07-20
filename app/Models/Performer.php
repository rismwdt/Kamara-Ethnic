<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Performer extends Model
{
    use HasFactory;

    protected $table = 'performers';

    protected $fillable = [
        'name',
        'gender',
        'category',
        'phone',
        'account_number',
        'bank_name',
        'status',
        'notes'
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_performers');
    }
    
}
