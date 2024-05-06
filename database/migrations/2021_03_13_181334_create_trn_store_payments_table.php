<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStorePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_payments', function (Blueprint $table) {
            $table->bigincrements('payment_id');
            $table->biginteger('order_item_id')->unsigned();
            $table->biginteger('order_id')->unsigned();
            $table->biginteger('delivery_boy_id')->unsigned();
            $table->biginteger('customer_id')->unsigned();
            $table->biginteger('payment_type_id')->unsigned();
            $table->biginteger('store_id')->unsigned();
            $table->string('store_commision_percentage');
            $table->string('admin_commision_amount');
            $table->string('return_amount');
            $table->string('total_amount');
             $table->foreign('store_id')->references('store_id')
            ->on('mst_stores');
            $table->foreign('payment_type_id')->references('payment_type_id')
            ->on('sys_payment_types');
            $table->foreign('order_id')->references('order_id')
            ->on('trn_store_orders');
            $table->foreign('order_item_id')->references('order_item_id')
            ->on('trn_order_items');
            $table->foreign('customer_id')->references('customer_id')
                ->on('trn_store_customers');
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
        Schema::dropIfExists('trn_store_payments');
    }
}
