<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnDeliveryBoyOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_delivery_boy_orders', function (Blueprint $table) {
           $table->bigincrements('delivery_boy_order_id');
            $table->biginteger('order_item_id')->unsigned();
             $table->biginteger('order_id')->unsigned();
             $table->biginteger('store_id')->unsigned();
             $table->biginteger('status_id')->unsigned();
             $table->datetime('assigned_date_time');
             $table->datetime('delivery_date_time');
             $table->datetime('Expected_date_time');
             $table->tinyinteger('delivery_status');
             $table->biginteger('payment_type_id');
             $table->foreign('order_item_id')->references('order_item_id')
            ->on('trn_order_items');
            $table->foreign('payment_type_id')->references('payment_type_id')
            ->on('sys_payment_types');
            $table->foreign('store_id')->references('store_id')
                ->on('mst_stores');
            $table->foreign('order_id')->references('order_id')
                ->on('trn_store_orders');
            $table->foreign('status_id')->references('status_id')
                ->on('sys_store_order_status');
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
        Schema::dropIfExists('trn_delivery_boy_orders');
    }
}
