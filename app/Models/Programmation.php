<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Programmation extends Model
{
    protected $fillable = ['user_id', 'schedule_id', 'category_id', 'title', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'loop_days'];

    protected $with = ['user', 'schedule', 'spaces.space', 'category', 'users.user'];

    protected $appends = ["comments_count", "notes_count", "links_count"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function spaces()
    {
        return $this->hasMany(ProgrammationSpace::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function users()
    {
        return $this->hasMany(ProgrammationUser::class);
    }

    public function comments()
    {
        return $this->hasMany(ProgrammationComment::class);
    }

    public function links()
    {
        return $this->hasMany(ProgrammationLink::class);
    }

    public function notes()
    {
        return $this->hasMany(ProgrammationNote::class);
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function getNotesCountAttribute()
    {
        return $this->notes()->count();
    }

    public function getLinksCountAttribute()
    {
        return $this->links()->count();
    }

    public function getLoopDaysAttribute($value)
    {
        if (!$value) return [];

        return json_decode("[{$value}]");
    }
}
