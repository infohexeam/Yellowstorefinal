<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreLinkSubadminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_link_subadmins', function (Blueprint $table) {
              $table->bigincrements('store_link_subadmin_id');
             $table->biginteger('store_id')->unsigned();
             $table->biginteger('subadmin_id')->unsigned();
             $table->foreign('store_id')->references('store_id')
                ->on('mst_stores');
            $table->foreign('subadmin_id')->references('id')
                ->on('users');
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
        Schema::dropIfExists('mst_store_link_subadmins');
    }
}
