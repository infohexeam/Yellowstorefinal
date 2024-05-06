<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnPosLocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_pos_locks', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->string('order_uid')->nullable();
            $table->integer('product_varient_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('status')->default(1);
            $table->string('ip_address')->nullable();
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
        Schema::dropIfExists('trn_pos_locks');
    }
}
