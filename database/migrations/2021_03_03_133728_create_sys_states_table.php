<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_states', function (Blueprint $table) {
            $table->bigincrements('state_id');
            $table->string('state_name',45);
            $table->biginteger('country_id')->unsigned();
            $table->foreign('country_id')->references('country_id')
            ->on('sysy_countries');
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
        Schema::dropIfExists('sys_states');
    }
}
