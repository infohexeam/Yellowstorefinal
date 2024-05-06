<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnPointsToRupeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_points_to_rupees', function (Blueprint $table) {
            $table->bigincrements('points_to_rupees_id');
            $table->integer('point');
            $table->integer('rupee');
            $table->boolean('isActive')->default(0);
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
        Schema::dropIfExists('trn_points_to_rupees');
    }
}
