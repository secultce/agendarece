<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomHoliday extends Model
{
    protected $fillable = ['sector_id', 'name', 'start_at', 'end_at'];

    protected $with = ['sector'];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
