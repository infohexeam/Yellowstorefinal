<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnSubAdminPaymentSettlmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_sub_admin_payment_settlments', function (Blueprint $table) {
            $table->bigincrements('sub_admin_payment_settlments_id');
            $table->biginteger('order_id')->unsigned();
            $table->biginteger('store_id')->unsigned();
            $table->biginteger('subadmin_id')->unsigned();
            $table->string('commision_percentage');
            $table->string('commision_amount');
            $table->string('sub_admin_commision');

             $table->foreign('subadmin_id')->references('id')
            ->on('users');
            $table->foreign('store_id')->references('store_id')
            ->on('mst_stores');
            $table->foreign('order_id')->references('order_id')
            ->on('trn_store_orders');
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
        Schema::dropIfExists('trn_sub_admin_payment_settlments');
    }
}
