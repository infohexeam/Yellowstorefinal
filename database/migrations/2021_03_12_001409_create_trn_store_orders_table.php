<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_orders', function (Blueprint $table) {
             $table->bigincrements('order_id');
             $table->string('order_number');
             $table->biginteger('customer_id')->unsigned();
             $table->biginteger('order_item_id')->unsigned();
             $table->biginteger('product_id')->unsigned();
             $table->biginteger('store_id')->unsigned();
             $table->decimal('product_total_amount',8,2);
             $table->string('shipping_address');
             $table->biginteger('country_id')->unsigned();
             $table->biginteger('state_id')->unsigned();
             $table->biginteger('district_id')->unsigned();
             $table->string('quantity');
             $table->string('shipping_landmark');
             $table->string('shipping_pincode');
             $table->string('coupon_discount_percentage');
             $table->date('delivery_date');
             $table->biginteger('payment_type_id')->unsigned();
             $table->biginteger('status_id')->unsigned();

            $table->foreign('status_id')->references('status_id')
            ->on('sys_store_order_status');
             $table->foreign('payment_type_id')->references('payment_type_id')
            ->on('sys_payment_type');
             $table->foreign('order_item_id')->references('order_item_id')
            ->on('trn_order_items');
            $table->foreign('country_id')->references('country_id')
            ->on('sys_countries');
             $table->foreign('state_id')->references('state_id')
            ->on('sys_states');
            $table->foreign('district_id')->references('district_id')
            ->on('mst_districts');
             $table->foreign('store_id')->references('store_id')
                ->on('mst_store_products');
            $table->foreign('product_id')->references('product_id')
                ->on('mst_stores');
            $table->foreign('customer_id')->references('customer_id')
                ->on('trn_store_customers');
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
        Schema::dropIfExists('trn_store_orders');
    }
}
