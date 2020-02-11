<?php

namespace App;

use App\Services\CustomerProfileCompleted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;

class Customer extends Model
{
    use Billable, Notifiable;

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
        return $this->hasMany(CustomerSubscription::class);
    }

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function activeSponsorshipsCodes()
    {
        return $this->hasMany(Sponsorship::class)->whereNotNull("date")->select("code");
    }

    public function inactiveSponsorshipsCodes()
    {
        return $this->hasMany(Sponsorship::class)->whereNull("date")->select("code");
    }

    public function requests()
    {
        return $this->hasMany(GymRequests::class);
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

        self::created(function ($model) {
            $model->sponsorships()->save(new Sponsorship(["date" => now(), "code" => Str::random(rand(6, 8))]));
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

    public function routeNotificationForMail($notification)
    {
        return $this->account->email;
    }
}
