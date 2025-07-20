<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationEstimate extends Model
{
    protected $fillable = [
        'from_location_id',
        'to_location_id',
        'distance_km',
        'estimated_mnt'
    ];

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}
