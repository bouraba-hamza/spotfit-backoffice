<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gym extends Model
{

    protected $with = ['supervisor'];

    protected $table = "gyms";

    protected $fillable = [
        "group_id",
        "logo",
        "name",
        "rate",
        "qrcode",
        "class_id",
        "facilities",
        "covers",
        "summary",
        "planning",

    ];

    public function subscriptions()
    {
        return $this->hasMany(\App\GymSubscriptionType::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facilitie::class,'gym_facilities',"gym_id",'facility_id');
    }

    public function activities()
    {
        return $this->belongsToMany(Activitie::class,'gym_activities',"gym_id",'activity_id');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function medal()
    {
        return $this->hasOne(Classe::class, 'id', 'class_id');
    }

    public function receptionist()
    {
        return $this->hasOne(Receptionist::class);
    }

    public function supervisor()
    {
        return $this->hasOne(Supervisor::class);
    }


}


