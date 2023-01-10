<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Configuration extends Model
{
    protected $fillable = ['logo', 'contact', 'copyright'];

    protected $appends = ['logo_url', 'logo_content'];

    public function getLogoUrlAttribute()
    {
        if (!$this->logo) return;

        return Storage::url($this->logo);
    }

    public function getLogoContentAttribute()
    {
        if (!$this->logo) return;

        return Storage::disk('public')->get($this->logo);
    }
}
