<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class customerSubscription extends Pivot
{

    protected $fillable = [
        "customer_id",
        "price",
        "qrcode",
        "gym_subscription_type",
        "consumed_at",
        "payment_method_id",
        "remaining_sessions",
    ];

//group_subscription_id
//group_subscription_type_id


    public function customer()
    {

        return $this->hasOne(Customer::class, "id", "customer_id");

    }

    public function statues()
    {

        return $this->belongsToMany(Status::class, 'customer_subscription_statuses', "customer_subscription_id", 'status_id');

    }
}
