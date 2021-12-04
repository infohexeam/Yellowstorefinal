<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstDistrictsTable extends Migration
{
    /** 
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_districts', function (Blueprint $table) {
            $table->bigincrements('district_id');
            $table->string('district_name',45);
            $table->biginteger('state_id')->unsigned();
            $table->foreign('state_id')->references('state_id')
            ->on('sys_states');
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
        Schema::dropIfExists('mst_districts');
    }
}
