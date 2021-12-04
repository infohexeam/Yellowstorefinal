<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_store_otp_verify extends Model
{
     protected $table ="trn_store_otp_verify";
	 protected $primaryKey = "store_otp_verify_id";

    protected $fillable = [
    					    'store_otp_verify_id','store_id','store_otp_expirytime','store_otp',
    						  ];

    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   } 						  
   
}
