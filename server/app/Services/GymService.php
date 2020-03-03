<?php


namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;

class GymService
{
    public static function visitors(int $gymId)
    {
        return DB::table('customers AS c')
            ->selectRaw('concat(c.firstName, " ", c.lastName) AS fullName, c.avatar, s.date')
            ->Join('customer_subscription AS cs', 'cs.customer_id', 'c.id')
            ->Join('sessions AS s', 's.customer_subscription_id', 'cs.id')
            ->where('s.gym_id', $gymId)
            ->orderByDesc('date')
            /*->whereDate("date", Carbon::now())*/
            ->get();
    }
}
