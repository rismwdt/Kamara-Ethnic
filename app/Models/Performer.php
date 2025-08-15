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
        'performer_role_id',
        'is_active',
        'phone',
        'account_number',
        'bank_name',
        'status',
        'notes'
    ];

    public function role()
    {
        return $this->belongsTo(PerformerRole::class, 'performer_role_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_performers');
    }
}
