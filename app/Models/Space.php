<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Space extends Model
{
    protected $fillable = ['icon', 'name', 'active'];

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
}
