<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Programmation extends Model
{
    protected $fillable = ['schedule_id', 'category_id', 'title', 'description', 'start_date', 'end_date', 'start_time', 'end_time'];

    protected $with = ['spaces.space', 'category', 'users'];

    public function spaces()
    {
        return $this->hasMany(ProgrammationSpace::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function users()
    {
        return $this->hasMany(ProgrammationUser::class);
    }

    public function comments()
    {
        return $this->hasMany(ProgrammationComment::class);
    }

    public function links()
    {
        return $this->hasMany(ProgrammationLink::class);
    }

    public function notes()
    {
        return $this->hasMany(ProgrammationNote::class);
    }
}
