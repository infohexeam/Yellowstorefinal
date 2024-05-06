<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreLinkAgencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_link_agency', function (Blueprint $table) {
            $table->bigincrements('link_id');
            $table->biginteger('store_id')->unsigned();
            $table->biginteger('agency_id')->unsigned();
            $table->foreign('agency_id')->references('agency_id')
            ->on('mst_store_agencies');     
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
        Schema::dropIfExists('mst_store_link_agency');
    }
}
