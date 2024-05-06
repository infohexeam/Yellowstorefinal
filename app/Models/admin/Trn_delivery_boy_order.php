<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_delivery_boy_order extends Model
{
    protected $primaryKey = "delivery_boy_order_id";

    protected $fillable = [
    					    'delivery_boy_order_id','order_item_id','order_id','store_id','delivery_boy_id','delivery_status_id','assigned_date_time','delivery_date_time','Expected_date_time','delivery_status','payment_status','payment_type_id',

    						  ];


    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   } 
     public function order()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_order','order_id','order_id');
   }
  public function deliveryboy()
   {
   	return $this->belongsTo('App\Models\admin\Mst_delivery_boy','delivery_boy_id','delivery_boy_id');
   }
   public function payment_type()
   {
   	return $this->belongsTo('App\Models\admin\Sys_payment_type','payment_type_id','payment_type_id');
   } 						  
    public function order_item()
   {
    return $this->belongsTo('App\Models\admin\Trn_store_order_item','order_item_id','order_item_id');
   }
    public function status()
   {
    return $this->belongsTo('App\Models\admin\Sys_store_order_status','delivery_status_id','status_id');
   } 

}
