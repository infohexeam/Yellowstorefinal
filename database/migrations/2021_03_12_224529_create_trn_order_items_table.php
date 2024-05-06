<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_order_items', function (Blueprint $table) {
             $table->bigincrements('order_item_id');
             $table->biginteger('product_varient_id')->unsigned();
             $table->biginteger('customer_id')->unsigned();
             $table->biginteger('store_id')->unsigned();
             $table->biginteger('delivery_boy_id')->unsigned();
             $table->string('store_commision_percentage');
             $table->tinyinteger('cart_status');
             $table->integer('quantity');
             $table->decimal('unit_price',8,2);
             $table->decimal('total_amount',8,2);
             $table->tinyinteger('delivery_status');
             $table->string('discount_percentage');
             $table->biginteger('payment_type_id')->unsigned();
             $table->date('order_date');
             $table->date('pay_date');
             $table->date('delivery_date');
             $table->foreign('product_varient_id')->references('product_varient_id')
            ->on('mst_store_product_varients');
             $table->foreign('payment_type_id')->references('payment_type_id')
            ->on('sys_payment_type');
             $table->foreign('customer_id')->references('customer_id')
                ->on('trn_store_customers');
            $table->foreign('store_id')->references('store_id')
                ->on('mst_stores');
            $table->foreign('delivery_boy_id')->references('delivery_boy_id')
                ->on('mst_delivery_boys');
                 $table->foreign('payment_type_id')->references('payment_type_id')
            ->on('sys_payment_type');
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
        Schema::dropIfExists('trn_order_items');
    }
}
