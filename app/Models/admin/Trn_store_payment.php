<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_store_payment extends Model
{
    protected $primaryKey = "payment_id";

    protected $fillable = [
    					     'payment_id','order_item_id','order_id','customer_id','payment_type_id','store_id','store_commision_percentage','delivery_boy_id','admin_commision_amount','return_amount','total_amount',
    						  ];

    public function customer()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
   } 	
	public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   } 	

   public function subadmin()
   {
   	return $this->belongsTo('App\User','App\Models\admin\Mst_store','id','subadmin_id');
   }


	public function payment_type()
   {
   	return $this->belongsTo('App\Models\admin\Sys_payment_type','payment_type_id','payment_type_id');
   }

   

    public function order_data()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_order','order_id');
   } 
    public function order_item()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_order_item','order_item_id','order_item_id');
   } 
    public function delivery_boy()
   {
    return $this->belongsTo('App\Models\admin\Mst_delivery_boy','delivery_boy_id','delivery_boy_id');
   } 
}
