<?php

namespace App;

use App\Mail\AccountCreated;
use App\Services\AccountService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Account extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasRoles;
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'username', 'email', 'password', 'disabled', 'lastLogin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /***
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function accountable()
    {
        return $this->morphTo();
    }

    public function scopeDisabled($query, $arg = 1)
    {
        return $query->where('disabled', $arg);
    }

    public function scopeVerified(Builder $query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scope(Builder $query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            AccountService::assignRole($model);

            // Send verification email to account inbox
            if(config('app.env') === 'production') {
                 Mail::to($model->email)->later(1, new AccountCreated($model));
            }
        });
    }
}
