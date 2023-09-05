<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomHoliday extends Model
{
    protected $fillable = ['sector_id', 'name', 'start_at', 'end_at', 'body'];

    protected $with = ['sector'];

    protected $appends = ['start_formatted', 'end_formatted'];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function getStartFormattedAttribute()
    {
        return implode('/', array_reverse(explode('-', $this->start_at)));
    }

    public function getEndFormattedAttribute()
    {
        return implode('/', array_reverse(explode('-', $this->end_at)));
    }
}
