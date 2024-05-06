<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_documents', function (Blueprint $table) {
            $table->bigincrements('store_document_id');
            $table->string('store_document_license',100)->nullable();
            $table->string('store_document_gstin')->nullable();
            $table->string('store_document_file_head');
            $table->string('store_document_other_file');
            $table->biginteger('store_id')->unsigned();
            $table->foreign('store_id')->references('store_id')
            ->on('mst_stores');
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
        Schema::dropIfExists('mst_store_documents');
    }
}
