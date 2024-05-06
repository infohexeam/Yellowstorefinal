<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__sub_categories', function (Blueprint $table) {
            $table->bigincrements('sub_category_id');
            $table->biginteger('category_id');
            $table->biginteger('business_type_id');
            $table->string('sub_category_name',45)->unique();
            $table->string('sub_category_name_slug',45)->unique()->nullable();     
            $table->string('sub_category_icon')->nullable();
            $table->longText('sub_category_description');
            $table->tinyInteger('sub_category_status');
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('mst__sub_categories');
    }
}
