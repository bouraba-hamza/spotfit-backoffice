<?php


namespace App\Services;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CustomerSubscriptionService
{
    /*
     * description: retrieve the last status
     * of specific subscription
     */
    public function getSubscriptionStatus($subscriptionId)
    {
        return DB::table("statuses as s")
            ->selectRaw("s.name, css.datetime")
            ->leftJoin("customer_subscription_statuses as css", "css.status_id", "=", "s.id")
            ->where("css.customer_subscription_id", $subscriptionId)
            ->orderBy("datetime", "desc")->first();
    }

    /*
     * description: all the customer subscriptions
     * from the customer began using the app
     */
    public function getCustomerSubscriptions($customerId)
    {
        return DB::table("customers as c")
            ->selectRaw("cs.id, cs.consumed_at as consumption_date, cs.qrcode as qrcode, t.name as type, ss.duration,
            (SELECT s.name FROM statuses s
            LEFT JOIN customer_subscription_statuses css ON css.status_id = s.id
            WHERE css.customer_subscription_id = cs.id
            ORDER BY DATETIME DESC LIMIT 1) as current_status, g.name AS gym_name, a.formattedAddress AS gym_address, lower(cl.name) AS class")
            ->leftJoin("customer_subscription as cs", "c.id", "=", "cs.customer_id")
            ->leftJoin("gym_subscription_types as gst", "cs.gym_subscription_type", "=", "gst.id")
            ->leftJoin("types as t", "gst.type_id", "=", "t.id")
            ->leftJoin("subscriptions as ss", "ss.id", "=", "gst.subscription_id")
            ->leftJoin("gyms AS g", "gst.gym_id", "=", "g.id")
            ->leftJoin("classes AS cl", "g.class_id", "=", "cl.id")
            ->leftJoin("addresses AS a", function($q) {
                $q->on("g.id", "=", "a.addressable_id")->where("a.addressable_type", "=", "App\\Gym");
                /*
                    $q->on("a.addressable_type", "=", DB::raw("'App\\\\Gym'"));
                */
            })
            ->where("cs.customer_id", $customerId)
            ->orderBy("consumption_date", "desc")
            ->get()->map(function($subscription) {
                // consumption_date (date) +  duration (int)
                $subscription->end_date = date('Y-m-d H:i:s', strtotime("+".$subscription->duration." day", strtotime($subscription->consumption_date)));

                // end_date (date) -  now (date)
                if($subscription->end_date) {
                    $earlier = new \DateTime($subscription->end_date);
                    $later = new \DateTime(date('Y-m-d H:i:s'));
                    $diff = $later->diff($earlier)->format("%r%a");
                    $diff = $diff < 0 ? 0 : $diff;
                    $diff = $diff > $subscription->duration ? $subscription->duration : $diff;
                    $subscription->remaining_time = $diff;
                }

                // Load Sessions Under Requested
                $subscription->sessions = $this->getSubscriptionSessions($subscription->id);
                return $subscription;
            });
    }

    /*
     * description: get sessions related to a customer subscription
     */
    public function getSubscriptionSessions($subscriptionId) {
        return DB::table("sessions as s")->where("customer_subscription_id", $subscriptionId)
            ->selectRaw("s.date, g.id as gym_id,  g.name as gym_name, a.formattedAddress as gym_address")
            ->leftJoin("gyms as g", "s.gym_id",  "g.id")
            ->leftJoin("addresses as a", function($q) {
                $q->on("g.id", "a.addressable_id")->where("a.addressable_type", "App\\Gym");
            })
            ->get();
    }

    public function test() {

    }


}
