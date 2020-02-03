<?php


namespace App\Services;


class CustomerProfileCompleted
{
    public static function completed($customer)
    {
        $completed = 1;

        if (!$customer->gender)
        {
            $completed = 0;
        }
        if (!$customer->firstName)
        {
            $completed = 0;
        }
        if (!$customer->lastName)
        {
            $completed = 0;
        }
        if (!$customer->birthDay)
        {
            $completed = 0;
        }
        if (!$customer->phoneNumber)
        {
            $completed = 0;
        }
        if (!$customer->IDF)
        {
            $completed = 0;
        }
        if (!$customer->IDB)
        {
            $completed = 0;
        }

        return $completed;
    }
}
