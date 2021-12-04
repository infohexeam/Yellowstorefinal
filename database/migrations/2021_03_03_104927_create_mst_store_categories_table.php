<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_categories', function (Blueprint $table) {
            $table->bigincrements('category_id');
            $table->biginteger('parent_id');
            $table->string('category_name',45)->unique();
            $table->string('category_name_slug',45)->unique()->nullable();     
            $table->string('category_icon')->nullable();
            $table->longText('category_description');
            $table->tinyInteger('category_status');
            $table->timestamp('deleted_at');
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
        Schema::dropIfExists('mst_store_categories');
    }
}
