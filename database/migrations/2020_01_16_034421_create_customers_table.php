<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('billing_firstname');
            $table->string('billing_lastname');
            $table->string('username');
            $table->string('email',150);
            $table->string('billing_address1');
            $table->string('billing_address2');
            $table->string('billling_country');
            $table->string('billing_state');
            $table->string('billing_zip');
            $table->string('shipping_firstname');
            $table->string('shipping_lastname');
            $table->string('shipping_address1');
            $table->string('shipping_address2');
            $table->string('shipping_country');
            $table->string('shipping_state');
            $table->string('shipping_zip');
            $table->timestamps();
            $table->softDeletes();
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
