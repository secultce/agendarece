<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['sector', 'user', 'action'];

    protected $dates = ['created_at'];

    protected $appends = ['created_at_formatted'];

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('d/m/Y h:i');
    }
}
