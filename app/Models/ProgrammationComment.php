<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgrammationComment extends Model
{
    protected $fillable = ['programmation_id', 'user_id', 'comment'];

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
