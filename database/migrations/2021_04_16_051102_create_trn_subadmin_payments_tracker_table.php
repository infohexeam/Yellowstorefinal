<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnSubadminPaymentsTrackerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_subadmin_payments_tracker', function (Blueprint $table) {
            $table->bigincrements('subadmin_payments_tracker_id');
            $table->biginteger('subadmin_id')->unsigned();
            $table->integer('commision_paid');
            $table->text('payment_note')->nullable();
            $table->date('date_of_payment');
            $table->foreign('subadmin_id')->references('id')->on('users');
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
        Schema::dropIfExists('trn_subadmin_payments_tracker');
    }
}
