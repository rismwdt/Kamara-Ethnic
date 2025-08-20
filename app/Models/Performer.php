<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->belongsToMany(Booking::class, 'booking_performers')
            ->withPivot(['is_external', 'confirmation_status', 'agreed_rate'])
            ->withTimestamps();
    }

    public function scopeSchedulable(Builder $q): Builder
    {
        return $q->where('is_active', 1);
    }

    /** Internal = bukan eksternal */
    public function scopeInternal(Builder $q): Builder
    {
        return $q->where('is_external', 0);
    }

    /** Eksternal */
    public function scopeExternal(Builder $q): Builder
    {
        return $q->where('is_external', 1);
    }
}
