<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreOtpVerifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_otp_verify', function (Blueprint $table) {
             $table->bigincrements('store_otp_verify_id');
             $table->biginteger('store_id')->unsigned();
             $table->string('store_otp_expirytime',45);
             $table->string('store_otp',45);
            $table->foreign('store_id')->references('store_id')
            ->on('mst_stores');
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
        Schema::dropIfExists('trn_store_otp_verify');
    }
}
