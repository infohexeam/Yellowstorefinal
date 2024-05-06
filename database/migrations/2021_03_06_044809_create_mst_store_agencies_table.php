<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_agencies', function (Blueprint $table) {
            $table->bigincrements('agency_id');
            $table->string('agency_name',45)->unique();
            $table->string('agency_name_slug',45)->unique();     
            $table->string('agency_contact_person_name',45);
            $table->string('agency_contact_person_phone_number',45);
            $table->integer('agency_contact_number_2');
            $table->string('agency_website_link',45);
            $table->integer('agency_pincode');
            $table->longtext('agency_primary_address');
            $table->string('agency_email_address',45);
            $table->string('agency_username',45);
            $table->string('agency_password',45);
            $table->string('agency_logo');
            $table->tinyinteger('agency_account_status');
            $table->biginteger('country_id')->unsigned();
            $table->biginteger('business_type_id')->unsigned();
            $table->foreign('country_id')->references('country_id')
            ->on('sys_countries');
            $table->foreign('business_type_id')->references('business_type_id')
            ->on('mst_store_business_types');
             $table->biginteger('state_id')->unsigned();
            $table->foreign('state_id')->references('state_id')
            ->on('sys_states');
             $table->biginteger('district_id')->unsigned();
            $table->foreign('district_id')->references('district_id')
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
        Schema::dropIfExists('mst_agency_agencies');
    }
}
