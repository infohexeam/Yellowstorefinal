<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreBusinessTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_business_types', function (Blueprint $table) {
            $table->bigincrements('business_type_id');  
            $table->string('business_type_name',45)->unique();
            $table->string('business_type_name_slug',45)->unique()->nullable();     
            $table->string('business_type_icon')->nullable();
            $table->tinyInteger('business_type_status');
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
        Schema::dropIfExists('mst_store_business_types');
    }
}
