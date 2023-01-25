<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['sector_id', 'name', 'color'];

    protected $with = ['sector'];

    public function getColorAttribute($value)
    {
        if (strlen($value) > 7) return substr($value, 0, 7);

        return $value;
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
