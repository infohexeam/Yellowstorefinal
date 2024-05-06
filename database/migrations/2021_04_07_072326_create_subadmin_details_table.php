<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubadminDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_subadmin_details', function (Blueprint $table) {
            $table->bigincrements('subadmin_details_id');
            $table->text('subadmin_address');
            $table->integer('subadmin_commision_amount');
            $table->integer('phone');
            $table->biginteger('country_id');
            $table->biginteger('state_id');
            $table->biginteger('district_id');
            $table->biginteger('town_id');
            $table->integer('subadmin_commision_percentage');
            $table->biginteger('subadmin_id')->unsigned();
            $table->foreign('subadmin_id')->references('id')->on('users');
            $table->timestamps();


            $table->foreign('country_id')->references('country_id')
            ->on('sys_countries');
            $table->foreign('state_id')->references('state_id')
            ->on('sys_states');
            $table->foreign('district_id')->references('district_id')
            ->on('mst_districts');
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
        Schema::table('mst_subadmin_details', function (Blueprint $table) {
            //
        });
    }
}
