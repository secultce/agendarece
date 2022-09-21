<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgrammationLink extends Model
{
    protected $fillable = ['programmation_id', 'user_id', 'name', 'link'];

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
