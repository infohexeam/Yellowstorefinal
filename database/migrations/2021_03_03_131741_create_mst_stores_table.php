<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_stores', function (Blueprint $table) {
            $table->bigincrements('store_id');
            $table->string('store_name',45)->unique();
            $table->string('store_name_slug',45)->unique();     
            $table->string('store_contact_person_name',45);
            $table->string('store_contact_person_phone_number',45);
            $table->integer('store_contact_number_2');
            $table->string('store_website_link',45);
            $table->integer('store_pincode');
            $table->longtext('store_primary_address');
            $table->string('store_email_address',45);
            $table->string('store_username',45);
            $table->string('store_password',45);
            $table->tinyinteger('store_account_status');
            $table->biginteger('store_country_id')->unsigned();
            $table->foreign('store_country_id')->references('country_id')
            ->on('sys_countries');
             $table->biginteger('store_state_id')->unsigned();
            $table->foreign('store_state_id')->references('state_id')
            ->on('sys_states');
             $table->biginteger('store_district_id')->unsigned();
            $table->foreign('store_district_id')->references('district_id')
            ->on('mst_districts');

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
        Schema::dropIfExists('mst_stores');
    }
}
