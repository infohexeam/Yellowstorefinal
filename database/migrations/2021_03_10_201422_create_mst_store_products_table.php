<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_products', function (Blueprint $table) {
            
            $table->bigincrements('product_id');
            $table->string('product_name',100);
            $table->string('product_name_slug',100);
            $table->string('product_code',45);
            $table->biginteger('product_cat_id')->unsigned();
            $table->longtext('product_description');
            $table->longtext('product_delivery_info');
            $table->date('product_offer_from_date');
            $table->date('product_offer_to_date');
            $table->decimal('product_price',8,2); 
            $table->decimal('product_price_offer',8,2);
            $table->string('product_shipping_info');
            $table->string('product_base_image');
            $table->biginteger('store_id')->unsigned();
            $table->tinyinteger('product_status');
            $table->foreign('product_cat_id')->references('category_id')
            ->on('mst_store_categories');
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
        Schema::dropIfExists('mst_store_products');
    }
}
