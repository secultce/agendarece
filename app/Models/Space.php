<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Space extends Model
{
    protected $fillable = ['sector_id', 'icon', 'name', 'active'];

    protected $with = ['sector'];

    protected $appends = ['icon_url'];

    public function delete()
    {
        Storage::delete($this->icon);

        return parent::delete();
    }

    public function getIconUrlAttribute()
    {
        return Storage::url($this->icon);
    }

    public function setActiveAttribute($value)
    {
        $this->attributes['active'] = $value ? 1 : 0;
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
