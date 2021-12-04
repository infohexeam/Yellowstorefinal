<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnOrderInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_order_invoices', function (Blueprint $table) {
            $table->bigincrements('order_invoice_id');
            $table->biginteger('order_id')->unsigned();
            $table->date('invoice_date');
            $table->string('invoice_id');
            $table->foreign('order_id')->references('order_id')->on('trn_store_orders');
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
        Schema::dropIfExists('trn_order_invoices');
    }
}
