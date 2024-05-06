<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_countries', function (Blueprint $table) {
            $table->bigincrements('country_id');
            $table->string('country_name',45);
            $table->string('iso',45);
            $table->string('sortname',45);
            $table->string('iso3',45);
            $table->integer('numcode');
            $table->integer('phonecode');    
           
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
        Schema::dropIfExists('sys_countries');
    }
}
