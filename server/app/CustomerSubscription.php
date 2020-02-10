<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomerSubscription extends Pivot
{

    /*protected $with = [
        "gym",
        "type",
        "subscription",
    ];*/

    protected $fillable = [
        "customer_id",
        "gym_subscription_type",
        "price",
        "qrcode",
        "payment_method_id",
        "consumed_at",
        "remaining_sessions",
    ];


    public function customer()
    {
        return $this->hasOne(Customer::class, "id", "customer_id");
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'customer_subscription_statuses', "customer_subscription_id", 'status_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, "customer_subscription_id", "id");
    }

    public function gym()
    {
        return $this->belongsToMany(Gym::class, "gym_subscription_types", "id", "gym_id");
    }

    public function type()
    {
        return $this->belongsToMany(Type::class, "gym_subscription_types", "id", "type_id");
    }

    public function subscription()
    {
        return $this->belongsToMany(Type::class, "gym_subscription_types", "id", "subscription_id");
    }
}
