<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GymSubscriptionType extends Model
{
    protected $fillable = [
        'gym_id',
        'subscription_id',
        'type_id',
        'price'
    ];

//    protected $with = ['subscription', 'type'];

    public function subscription() {
        return $this->hasOne(Subscription::class, 'id', 'subscription_id');
    }
    public function type() {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }
}
