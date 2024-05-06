<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstGlobalProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__global_products', function (Blueprint $table) {
            $table->bigincrements('global_product_id');
            $table->string('product_name',200)->nullable();
            $table->string('product_name_slug',100)->nullable();
            $table->longtext('product_description')->nullable();
            $table->decimal('regular_price',8,2)->default(0); 
            $table->decimal('sale_price',8,2)->default(0); 
            $table->biginteger('tax_id')->unsigned()->default(0); 
            $table->integer('min_stock')->default(0); 
            $table->string('product_code',100)->nullable();
            $table->biginteger('business_type_id')->unsigned()->default(0); 
            $table->biginteger('color_id')->unsigned()->default(0); 
            $table->string('product_brand',100)->nullable();
            $table->biginteger('attr_group_id')->unsigned()->default(0); 
            $table->biginteger('attr_value_id')->unsigned()->default(0); 
            $table->biginteger('product_cat_id')->unsigned()->default(0); 
            $table->biginteger('vendor_id')->unsigned()->default(0); 
            $table->string('product_base_image',100)->nullable();
            $table->date('created_date')->nullable();
            $table->biginteger('created_by')->unsigned()->default(0); 
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
        Schema::dropIfExists('mst__global_products');
    }
}
