<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerSubscriptionStatus extends Model
{
    //
    protected $fillable=[
        'customer_subscription_id',
        'status_id',
        'datetime',
    ];
}
