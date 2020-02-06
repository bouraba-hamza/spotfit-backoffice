<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GymRequests extends Model
{
    protected $table = "customer_gym_requests";

    public $fillable = [
        "customer_id",
        "gymName",
        "address",
        "phoneNumber",
        "picture",
    ];
}
