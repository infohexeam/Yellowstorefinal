<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStorePaymentsTrackerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_payments_tracker', function (Blueprint $table) {
            $table->bigincrements('store_payments_tracker_id');
            $table->biginteger('store_id')->unsigned();
            $table->integer('commision_paid');
            $table->text('payment_note')->nullable();
            $table->date('date_of_payment');
            $table->foreign('store_id')->references('store_id')->on('mst_stores');
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
        Schema::dropIfExists('trn_store_payments_tracker');
    }
}
