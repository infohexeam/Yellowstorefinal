<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_customer_reward extends Model
{
    
    protected $primaryKey = "reward_id";

    protected $fillable = [
    					     'reward_id','transaction_type_id',
    					     'reward_points_earned','reward_approved_date',
    					     'reward_point_expire_date','reward_point_status',
    					     'customer_id','order_id','discription'

    						  ];
 public function customer()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
   }
 public function order()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_order','order_id','order_id');
   }
   public function reward_type()
   {
    return $this->belongsTo('App\Models\admin\Trn_customer_reward_transaction_type','transaction_type_id','transaction_type_id');
   }      						  
}
