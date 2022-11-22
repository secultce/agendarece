<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleUser extends Model
{
    protected $fillable = ['user_id', 'schedule_id'];
}
