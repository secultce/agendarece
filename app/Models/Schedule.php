<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['sector_id', 'user_id', 'name', 'private', 'calendar_icons'];

    protected $with = ['users', 'shares', 'sector'];

    public function programmations()
    {
        return $this->hasMany(Programmation::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'schedule_users');
    }

    public function shares()
    {
        return $this->belongsToMany(User::class, 'schedule_shares');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
