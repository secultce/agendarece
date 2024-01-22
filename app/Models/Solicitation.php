<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitation extends Model
{
    protected $fillable = ['occupation_id', 'user_id', 'schedule_id', 'category_id', 'title', 'description', 'parental_rating', 'start_date', 'end_date', 'start_time', 'end_time', 'loop_days', 'accessibilities'];

    protected $with = ['user', 'schedule', 'spaces.space', 'category', 'occupation'];

    protected $appends = ["parental_rating_alias"];

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
        return $this->hasMany(SolicitationSpace::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function getLoopDaysAttribute($value)
    {
        return json_decode("[{$value}]");
    }

    public function getAccessibilitiesAttribute($value)
    {
        return json_decode("[{$value}]");
    }

    public function getParentalRatingAliasAttribute()
    {
        switch ($this->parental_rating) {
            case 0: return "Livre"; break;
            case 1: return "10+"; break;
            case 2: return "12+"; break;
            case 3: return "14+"; break;
            case 4: return "16+"; break;
            case 5: return "18+"; break;
        }
    }
}
