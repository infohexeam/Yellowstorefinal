<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model; 

class Mst_Coupon extends Model
{
    protected $table ="mst__coupons";
    protected $primaryKey = "coupon_id";
     protected $fillable = [
                         'store_id','coupon_code','coupon_type','discount_type',
                         'valid_to','valid_from','coupon_status','discount','min_purchase_amt'
                           ];
     public function store() //town  relation
    {
        return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
    }
}
