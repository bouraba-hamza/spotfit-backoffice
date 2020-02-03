<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupSubscriptionType extends Model
{

          protected $fillable = [
              "group_id",
              "subscription_id",
              "type_id",
              "price"
          ];
}
