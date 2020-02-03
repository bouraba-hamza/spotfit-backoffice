<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSubscriptionStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_subscription_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_subscription_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->timestamp("datetime");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_subscription_statuses');
    }
}
