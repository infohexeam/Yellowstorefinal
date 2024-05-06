<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_settings', function (Blueprint $table) {
            $table->bigincrements('store_setting_id');
            $table->biginteger('store_id')->unsigned();
            $table->integer('service_start');
            $table->integer('service_end');
            $table->integer('delivery_charge');
            $table->integer('packing_charge');
            // $table->foreign('store_id')->references('store_id')->on('mst_stores');
            //            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_store_settings');
    }
}
