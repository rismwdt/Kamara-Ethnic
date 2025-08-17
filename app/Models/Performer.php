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
        'is_external',
        'notes'
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_external' => 'boolean',
        ];

    public function role()
    {
        return $this->belongsTo(PerformerRole::class, 'performer_role_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_performers') // ← perbaiki ini
            ->withPivot(['is_external', 'confirmation_status', 'agreed_rate'])
            ->withTimestamps();
    }

    public function scopeSchedulable($q)
    {
        return $q->where('status', 'aktif')->where('is_active', true);
    }

    public function scopeInternal($q)
    {
        return $q->where('is_external', false);
    }

    public function scopeExternal($q)
    {
        return $q->where('is_external', true);
    }
}
