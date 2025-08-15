<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformerRole extends Model
{
    protected $table = 'performer_roles';
    protected $fillable = ['name'];
}
