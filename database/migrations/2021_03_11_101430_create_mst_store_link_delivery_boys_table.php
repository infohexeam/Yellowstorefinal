<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreLinkDeliveryBoysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_link_delivery_boys', function (Blueprint $table) {
             $table->bigincrements('store_link_delivery_boy_id');
             $table->biginteger('store_id')->unsigned();
             $table->biginteger('delivery_boy_id')->unsigned();
             $table->foreign('store_id')->references('store_id')
                ->on('mst_stores');
            $table->foreign('delivery_boy_id')->references('delivery_boy_id')
                ->on('mst_delivery_boys');
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
        Schema::dropIfExists('mst_store_link_delivery_boys');
    }
}
