<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activitie extends Model
{

    protected $table = "activities";

    protected $fillable = [
        "name",
        "icon",

    ];

    public function activities()
    {
        return $this->belongsToMany('App\Activitie', "gym_activities");
    }

    public function gyms()
    {
        return $this->belongsToMany(Gym::class, "gym_activities", "activity_id", "gym_id");
    }
}


