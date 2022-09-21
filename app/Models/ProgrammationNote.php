<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgrammationNote extends Model
{
    protected $fillable = ['programmation_id', 'user_id', 'note'];

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
