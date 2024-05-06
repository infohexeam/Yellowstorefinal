<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_order_invoice extends Model
{
    protected $primaryKey = "order_invoice_id";
    protected $table = "trn_order_invoices";

    protected $fillable = [


        'order_id','invoice_date',
        'invoice_id'
    						  ];


   public function order()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_order','order_id','order_id');
   }
}
