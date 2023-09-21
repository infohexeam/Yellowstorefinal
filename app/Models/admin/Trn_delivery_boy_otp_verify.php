<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_delivery_boy_otp_verify extends Model
{
     protected $table ="trn_delivery_boy_otp_verify";
	 protected $primaryKey = "delivery_boy_otp_verify_id";

    protected $fillable = ['delivery_boy_otp_verify_id','delivery_boy_id','delivery_boy_otp_expirytime','delivery_boy_otp',];

    public function delivery_boy()
   {
   	return $this->belongsTo('App\Models\admin\Mst_delivery_boy','delivery_boy_id','delivery_boy_id');
   } 						  
   
}
