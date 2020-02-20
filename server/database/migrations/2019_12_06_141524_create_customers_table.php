<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('qrcode')->nullable();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->date('birthDay')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->string('cin')->nullable();
            $table->string('IDF')->nullable();
            $table->string('IDB')->nullable();
            $table->string('jobTitle')->nullable();
            $table->longText('avatar')->nullable();
            $table->tinyInteger('completed')
                ->default(0)
                ->comment("will be 1 if the customer provides us gender/firstName/lastName/birthDay/phoneNumber/idF/idB");
            $table->tinyInteger('ambassador')->default(0);
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
        Schema::dropIfExists('customers');
    }
}
