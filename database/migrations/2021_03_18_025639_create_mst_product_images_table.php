<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_product_images', function (Blueprint $table) {
            $table->bigincrements('product_image_id');
            $table->string('product_image');
            $table->biginteger('product_varient_id')->unsigned();
            $table->foreign('product_varient_id')->references('product_varient_id')
            ->on('mst_store_product_varients');
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
        Schema::dropIfExists('mst_product_images');
    }
}
