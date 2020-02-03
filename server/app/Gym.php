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
        "planning",

    ];

activities



    public function subscriptions()
    {
        return $this->hasMany(\App\GymSubscriptionType::class);
    }

    public function activities()
    {
//        return $this->hasMany(Address::class, 'addressable');
    }

    public function gyms()
    {
        return $this->belongsToMany('App\Gym', "gyms");
    }

//    public function facilities()
//    {
//        return $this->hasMany('App\Gym', "gyms");
//    }

    public function facilities()
    {
        return $this->belongsToMany(Facilitie::class,'gym_facilities',"gym_id",'facility_id');

    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function medal()
    {
        return $this->hasOne(Classe::class, 'id', 'class_id');
    }

    public function format(Classe $class)
    {
        // $book->loadMissing('author');

        return [
            'id' => $class->id,
            'name' => $class->name,
        ];
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


