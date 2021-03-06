<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_id');
            $table->integer('customer_subscription_id')->comment("Can't be ever for the same customer")->nullable();
            $table->float('amount')->comment("formula: (customer subscription price) * (sponsorship-rate / 100)")->nullable();
            $table->string('code')->unique()->index();
            $table->timestamp('date')->comment("coupon is active whenever date is not NULL")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sponsorships');
    }
}
