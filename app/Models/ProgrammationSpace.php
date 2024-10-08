<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgrammationSpace extends Model
{
    protected $fillable = ['programmation_id', 'space_id'];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }
}
