<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomerSubscriptionStatus extends Pivot
{
    //
    protected $fillable=[
        'customer_subscription_id',
        'status_id',
        'datetime',
    ];
}
