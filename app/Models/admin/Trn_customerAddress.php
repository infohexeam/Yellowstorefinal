<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_customerAddress extends Model
{
    protected $table = "trn_customer_addresses";
    protected $primaryKey = "customer_address_id";

    protected $fillable = [
    					     'customer_id','address',
    					     'name','phone',
    					     'state','district',
    					     'street','pincode',
    					     'default_status',
    					     'longitude',
    					     'latitude',
    					     'place',

    						  ];
    public function customer()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
   }
   
   public function stateFunction()
   {
   	return $this->belongsTo('App\Models\admin\State','state','state_id');
   }
	public function districtFunction()
   {
   	return $this->belongsTo('App\Models\admin\District','district','district_id');
   }
   
}
