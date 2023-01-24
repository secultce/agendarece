<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sector_id', 'role_id', 'avatar', 'name', 'email', 'password', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['avatar_url', 'sector'];

    protected $with = ['role'];

    public function delete()
    {
        if ($this->avatar) Storage::delete($this->avatar);

        return parent::delete();
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) return null;

        return Storage::url($this->avatar);
    }

    public function hasSector()
    {
        return $this->hasOne(Sector::class);
    }

    public function belongsSector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function getSectorAttribute()
    {
        if ($this->role->tag === 'responsible') return $this->hasSector;

        return $this->belongsSector;
    }

    public function setActiveAttribute($value)
    {
        $this->attributes['active'] = $value ? 1 : 0;
    }
}
