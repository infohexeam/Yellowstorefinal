<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_customers', function (Blueprint $table) {
            $table->bigincrements('customer_id');
            $table->string('customer_firstname',100);
            $table->string('customer_lastname',100);
            $table->string('customer_email',100);
            $table->string('customer_address',100);
            $table->biginteger('country_id')->unsigned();
            $table->biginteger('state_id')->unsigned();
            $table->biginteger('district_id')->unsigned();
            $table->biginteger('town_id')->unsigned();
            $table->string('customer_location');
            $table->string('customer_pincode',100);
            $table->string('customer_bank_account',100);
            $table->string('customer_username',100);
            $table->string('customer_password',100);
            $table->tinyInteger('customer_profile_status');
            $table->tinyInteger('customer_otp_verify_status');
            $table->foreign('country_id')->references('country_id')
            ->on('sys_countries');
             $table->foreign('state_id')->references('state_id')
            ->on('sys_states');
            $table->timestamps();
            $table->foreign('district_id')->references('district_id')
            ->on('mst_districts');
            $table->foreign('town_id')->references('town_id')
            ->on('mst_towns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_store_customers');
    }
}
