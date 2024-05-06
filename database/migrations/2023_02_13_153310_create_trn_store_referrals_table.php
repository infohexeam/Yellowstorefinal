<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_referrals', function (Blueprint $table) {
            $table->id();
            $table->string('store_referral_number')->nullable();
            $table->string('store_id')->nullable();
            $table->string('refered_by_id')->nullable();
            $table->string('refered_by_number')->nullable();
            $table->string('joined_by_id')->nullable();
            $table->string('joined_by_number')->nullable();
            $table->string('referral_points')->nullable();
            $table->string('joiner_points')->nullable();
            $table->string('fop')->nullable();
            $table->string('joiner_type')->nullable();
            $table->string('order_id')->nullable();
            $table->string('reference_status')->nullable();
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
        Schema::dropIfExists('trn_store_referrals');
    }
}
