<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $with = ['address', 'account'];

    protected $fillable = [
        'firstName',
        'lastName',
        'gender',
        'birthDay',
        'phoneNumber',
        'cin',
        'jobTitle',
        'avatar',
    ];

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function account()
    {
        return $this->morphOne(account::class, 'accountable');
    }

    public function gyms() {
        return $this->hasManyThrough(Gym::class, Group::class);
    }

    public function scopeSingle(Builder $query)
    {
        return $query->has('gyms', '=', 1);
    }

    public function scopePoly(Builder $query)
    {
        return $query->has('gyms', '>=', 1);
    }

    public function group() {
        return $this->hasOne(Group::class);
    }
}
