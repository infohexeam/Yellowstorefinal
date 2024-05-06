<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_store_customer_otp_verify extends Model
{
    protected $primaryKey = "customer_otp_verify_id";
    protected $table = "trn_store_customer_otp_verify";

    protected $fillable = [
    					    'customer_otp_verify_id','customer_id','customer_otp_expirytime',
                            'customer_otp',
    						  ];

    public function customer()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
   } 		
}
