<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstDeliveryBoyAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_delivery_boy_availabilities', function (Blueprint $table) {
            $table->bigincrements('delivery_boy_availability_id');
            $table->biginteger('delivery_boy_id')->unsigned();
            $table->date('delivery_boy_available_day');
            $table->date('delivery_boy_available_time');
            $table->tinyinteger('delivery_boy_active_flag');
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
        Schema::dropIfExists('mst_delivery_boy_availabilities');
    }
}
