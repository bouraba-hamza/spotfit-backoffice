<?php


namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class AccountService
{
    public static function assignRole(Model $account)
    {
        $role = null;
        $accountable = $account->accountable()->first();

        if ($accountable instanceof \App\Admin)
            $role = 'admin';
        else if ($accountable instanceof \App\Partner)
            $role = 'partner';
        else if ($accountable instanceof \App\Customer)
            $role = 'customer';
        else if ($accountable instanceof \App\Trainer)
            $role = 'trainer';
        else if ($accountable instanceof \App\Supervisor)
            $role = 'supervisor';
        else if ($accountable instanceof \App\Receptionist)
            $role = 'receptionist';

        if ($role != null)
            $account->assignRole($role);

        return $role;
    }

    public static function customer($person)
    {
        if ($person instanceof \App\Customer)
            return true;

        return false;
    }

    public static function partner($person)
    {
        if ($person instanceof \App\Partner)
            return true;

        return false;
    }
}
