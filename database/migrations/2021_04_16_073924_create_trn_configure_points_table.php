<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnConfigurePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_configure_points', function (Blueprint $table) {
            $table->bigincrements('configure_points_id');
            $table->integer('registraion_points')->nullable();
            $table->integer('first_order_points')->nullable();
            $table->integer('referal_points')->nullable();
            $table->integer('rupee_points')->nullable();
            $table->integer('order_amount');
            $table->integer('points');
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
        Schema::dropIfExists('trn_configure_points');
    }
}
