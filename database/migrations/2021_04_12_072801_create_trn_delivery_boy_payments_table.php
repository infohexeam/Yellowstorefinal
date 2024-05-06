<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnDeliveryBoyPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_delivery_boy_payments', function (Blueprint $table) {
            $table->bigincrements('delivery_boy_payment_id');
            $table->biginteger('delivery_boy_id')->unsigned();
            $table->integer('commision_paid');
            $table->text('payment_note')->nullable();
            $table->date('date_of_payment');
            $table->foreign('delivery_boy_id')->references('delivery_boy_id')->on('mst_delivery_boys');
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
        Schema::dropIfExists('trn_delivery_boy_payments');
    }
}
