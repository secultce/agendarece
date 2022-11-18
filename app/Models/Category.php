<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'color'];

    public function getColorAttribute($value)
    {
        if (strlen($value) > 7) return substr($value, 0, 7);

        return $value;
    }
}
