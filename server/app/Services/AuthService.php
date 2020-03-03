<?php


namespace App\Services;

class AuthService
{
    public function connected($accountable = false)
    {
        $account = \JWTAuth::parseToken()->authenticate();
        if(!$account) return;
        return $accountable ? $account->accountable()->first() : $account;
    }
}
