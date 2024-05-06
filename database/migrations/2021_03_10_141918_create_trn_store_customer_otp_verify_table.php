<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreCustomerOtpVerifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_customer_otp_verify', function (Blueprint $table) {
             $table->bigincrements('customer_otp_verify_id');
             $table->biginteger('customer_id')->unsigned();
             $table->string('customer_otp_expirytime',45);
             $table->string('customer_otp',45);
             $table->foreign('customer_id')->references('customer_id')
            ->on('trn_store_customers');
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
        Schema::dropIfExists('trn_store_customer_otp_verify');
    }
}
