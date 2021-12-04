<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubadminIdColumnsToMstStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_stores', function (Blueprint $table) {
            $table->string('place',45);
            $table->biginteger('town_id')->unsigned();
            $table->integer('store_commision_amount');
            $table->biginteger('subadmin_id')->unsigned();
            $table->foreign('subadmin_id')->references('id')
            ->on('users'); 
            $table->foreign('town_id')->references('town_id')
            ->on('mst_towns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_stores', function (Blueprint $table) {
            //
        });
    }
}
