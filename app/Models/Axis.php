<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Axis extends Model
{
    use HasFactory;

    protected $table = "axes";

    protected $fillable = ['sector_id', 'name'];

    protected $with = ['sector'];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
