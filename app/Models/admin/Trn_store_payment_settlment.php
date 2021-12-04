<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_store_payment_settlment extends Model
{
    protected $primaryKey = "settlment_id";

    protected $fillable = [
    					     'settlment_id','store_commision_percentage','order_id','store_id','store_commision_amount','admin_commision_amount','total_amount','store_commision_percentage','commision_paid','commision_to_be_paid',
    						  ];

    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }
   public function order()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_order','order_id','order_id');
   }
}
