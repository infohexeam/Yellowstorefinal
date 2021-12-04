<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_store_order_item extends Model
{
    protected $primaryKey = "order_item_id";
    protected $table = "trn_order_items";
    

    protected $fillable = [
                             'order_id',
    					     'order_item_id',
                     'product_id',
                     'product_varient_id',
                       'customer_id',
                       'store_id',
                       'delivery_boy_id',
                       'store_commision_percentage',
                       'cart_status',
                       'quantity',
                       'unit_price',
                       'total_amount',
                       'delivery_status',
                       'discount_percentage',
                       'payment_type_id',
                       'order_date',
                       'pay_date',
                       'delivery_date',
                       'tick_status','delivery_boy_tick_status'
    						  ];
   public function customer()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
   } 	
	public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   } 

   public function product()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store_product','product_id','product_id');
   } 

   public function payment_type()
   {
   	return $this->belongsTo('App\Models\admin\Sys_payment_type','payment_type_id','payment_type_id');
   } 
   public function delivery_boy()
   {
   	return $this->belongsTo('App\Models\admin\Mst_delivery_boy','delivery_boy_id','delivery_boy_id');
   }
   public function product_varient()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store_product_varient','product_varient_id','product_varient_id');
   } 
}
