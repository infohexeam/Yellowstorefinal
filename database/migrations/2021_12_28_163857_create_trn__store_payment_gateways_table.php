<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStorePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__store_payment_gateways', function (Blueprint $table) {
            $table->bigincrements('spg_id');
            $table->biginteger('store_id')->unsigned()->nullable();
            $table->biginteger('payment_gateway_id')->unsigned()->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret_key')->nullable();
            $table->tinyInteger('pg_status')->nullable();
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
        Schema::dropIfExists('trn__store_payment_gateways');
    }
}
