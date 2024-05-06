<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstTownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_towns', function (Blueprint $table) {
            $table->bigincrements('town_id');
            $table->string('town_name',45);
            $table->biginteger('district_id')->unsigned();
            $table->foreign('district_id')->references('district_id')->on('mst_districts');
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
        Schema::dropIfExists('mst_towns');
    }
}
