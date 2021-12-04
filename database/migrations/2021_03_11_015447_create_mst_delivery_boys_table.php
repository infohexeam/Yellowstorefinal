<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstDeliveryBoysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_delivery_boys', function (Blueprint $table) {
            $table->bigincrements('delivery_boy_id');
            $table->string('delivery_boy_name');
            $table->string('delivery_boy_mobile');
            $table->string('delivery_boy_email');
            $table->string('delivery_boy_image');
            $table->longtext('delivery_boy_address');
            $table->string('vehicle_number');
            $table->biginteger('country_id')->unsigned();
            $table->biginteger('state_id')->unsigned();
            $table->biginteger('district_id')->unsigned();
            $table->biginteger('vehicle_type_id')->unsigned();
            $table->biginteger('delivery_boy_availability_id')->unsigned();
            $table->biginteger('store_id')->unsigned();
            $table->string('delivery_boy_username')->unique();
            $table->string('delivery_boy_password');
            $table->tinyinteger('delivery_boy_status');
            $table->integer('delivery_boy_commision_amount');
           
            $table->foreign('country_id')->references('country_id')
            ->on('sys_countries');
            $table->foreign('state_id')->references('state_id')
            ->on('sys_states');
            $table->foreign('district_id')->references('district_id')
            ->on('mst_districts');
            $table->foreign('store_id')->references('store_id')
            ->on('mst_stores');
            $table->foreign('vehicle_type_id')->references('vehicle_type_id')
            ->on('sys_vehicle_types');
            $table->foreign('delivery_boy_availability_id')->references('delivery_boy_availability_id')
             ->on('mst_delivery_boy_availabilities');
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
        Schema::dropIfExists('mst_delivery_boys');
    }
}
