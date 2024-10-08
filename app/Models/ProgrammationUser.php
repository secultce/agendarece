<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgrammationUser extends Model
{
    protected $fillable = ['programmation_id', 'user_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
