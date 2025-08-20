<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerformerRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'performer_role_id',
        'quantity',
        'notes'
    ];

    public function role()
    {
        return $this->belongsTo(PerformerRole::class, 'performer_role_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function performerRole()
    {
        return $this->belongsTo(PerformerRole::class, 'performer_role_id');
    }
}
