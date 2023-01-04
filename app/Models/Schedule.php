<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['user_id', 'name', 'private'];

    protected $with = ['users', 'shares'];

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
}
