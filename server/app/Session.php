<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "qrcode",
        "customer_subscription_id",
        "gym_id",
    ];
}
