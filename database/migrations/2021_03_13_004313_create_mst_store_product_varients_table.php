<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreProductVarientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_product_varients', function (Blueprint $table) {
             $table->bigincrements('product_varient_id');
             $table->decimal('product_varient_price',8,2);
             $table->decimal('product_varient_offer_price',8,2);
             $table->date('product_varient_offer_from_date');
             $table->date('product_varient_offer_to_date');
             $table->string('product_varient_base_image');
             $table->biginteger('product_id')->unsigned();
             $table->biginteger('store_id')->unsigned();
             $table->foreign('product_id')->references('product_id')
                ->on('mst_store_products');
             $table->foreign('store_id')->references('store_id')
                ->on('mst_stores');
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
        Schema::dropIfExists('mst_store_product_varients');
    }
}
