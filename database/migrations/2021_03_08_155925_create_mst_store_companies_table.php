<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_companies', function (Blueprint $table) {
            $table->bigincrements('company_id');
            $table->string('company_name',45)->unique();
            $table->string('company_name_slug',45)->unique();     
            $table->string('company_contact_person_name',45);
            $table->string('company_contact_person_phone_number',45);
            $table->integer('company_contact_number_2');
            $table->string('company_website_link',45);
            $table->integer('company_pincode');
            $table->longtext('company_primary_address');
            $table->string('company_email_address',45);
            $table->string('company_username',45);
            $table->string('company_password',45);
            $table->string('company_logo');
            $table->tinyinteger('company_account_status');
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
        Schema::dropIfExists('mst_store_companies');
    }
}
