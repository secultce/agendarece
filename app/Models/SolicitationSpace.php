<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitationSpace extends Model
{
    protected $fillable = ['solicitation_id', 'space_id'];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }
}
