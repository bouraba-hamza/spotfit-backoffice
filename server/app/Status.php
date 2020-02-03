<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = [
        "name",
        "color",
    ];

    public function customersubscription()
    {
        return $this->belongsToMany(customerSubscription::class,'customer_subscription_statuses',"status_id",'customer_subscription_id');
    }

}
