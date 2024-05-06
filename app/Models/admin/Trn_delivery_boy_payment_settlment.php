<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_delivery_boy_payment_settlment extends Model
{
     protected $primaryKey = "delivery_boy_settlment_id";

    protected $fillable = [
    					     'delivery_boy_settlment_id','order_id','store_id','store_commision_amount','delivery_boy_commision_amount','total_amount','store_commision_percentage','commision_paid','commision_to_be_paid','delivery_boy_id',
    						  ];

    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   } 	
   public function order()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_order','order_id','order_id');
   }
   public function delivery_boy()
   {
    return $this->belongsTo('App\Models\admin\Mst_delivery_boy','delivery_boy_id','delivery_boy_id');
   }  
}
