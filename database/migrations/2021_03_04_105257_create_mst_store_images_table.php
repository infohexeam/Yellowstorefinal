<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_images', function (Blueprint $table) {
            $table->bigincrements('store_image_id');
            $table->string('store_image');
            $table->biginteger('store_id')->unsigned();
            $table->boolean('default_image')->default(0);
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
        Schema::dropIfExists('mst_store_images');
    }
}
