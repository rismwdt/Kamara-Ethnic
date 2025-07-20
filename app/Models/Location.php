<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'full_address'
    ];

    public function outgoingEstimates()
    {
        return $this->hasMany(LocationEstimate::class, 'from_location_id');
    }

    public function incomingEstimates()
    {
        return $this->hasMany(LocationEstimate::class, 'to_location_id');
    }
}
