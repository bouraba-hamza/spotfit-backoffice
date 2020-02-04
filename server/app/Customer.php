<?php

namespace App;

use App\Services\CustomerProfileCompleted;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Customer extends Model
{
    use Billable;

    protected $with = ['address', 'account'];

    protected $fillable = [
        'qrcode',
        'firstName',
        'lastName',
        'gender',
        'birthDay',
        'phoneNumber',
        'cin',
        'jobTitle',
        'avatar',
        'ambassador',
        'IDF',
        'IDB',
        'completed',
    ];

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function account()
    {
        return $this->morphOne(account::class, 'accountable');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class)
            ->using(customerSubscription::class)
            ->withPivot([
                "price",
                "consumed_at",
                "activated_at",
                "canceled_at",
            ])
            ->withTimestamps();
    }

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function favoritesGyms()
    {
        return $this->belongsToMany(Gym::class, 'favorite_gyms', "customer_id", 'gym_id');
    }

    protected static function boot()
    {
        parent::boot();

        self::updating(function ($model) {

            $completed = CustomerProfileCompleted::completed($model);
            // update the column completed
            $model->completed = $completed;
        });
    }

    public function like(Gym $gym)
    {
        return $this->favoritesGyms()->save($gym);
    }

    public function dislike($gymId)
    {
        return $this->favoritesGyms()->detach([$gymId]);
    }
}
