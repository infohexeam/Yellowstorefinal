<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Mst_delivery_boy extends Authenticatable
{
   use HasApiTokens;

	protected $table = "mst_delivery_boys";
   protected $primaryKey = "delivery_boy_id";

    protected $fillable = ['delivery_boy_id', 'town_id' ,'delivery_boy_name',
    'delivery_boy_mobile','delivery_boy_email','delivery_boy_image','delivery_boy_address',
    'vehicle_number','vehicle_type_id','delivery_boy_availability_id',
    'delivery_boy_username','password','country_id',
    'state_id','district_id','store_id','delivery_boy_status','delivery_boy_commision_amount','availability_status',
    'latitude','longitude'
							];

    public function AauthAcessToken()
    {
      return $this->hasMany('\App\Models\OauthAccessToken','user_id','delivery_boy_id');
    }

	public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   } 

   public function country()
   {
   	return $this->belongsTo('App\Models\admin\Country','country_id','country_id');
   }
   
   public function town()
   {
   	return $this->belongsTo('App\Models\admin\Town','town_id','town_id');
   }
   
   
	public function availability()
   {
   	return $this->belongsTo('App\Models\admin\Sys_delivery_boy_availability','delivery_boy_availability_id','availability_id');
   }
	public function vehicle_type()
   {
   	return $this->belongsTo('App\Models\admin\Sys_vehicle_type','vehicle_type_id','vehicle_type_id');
   }

   public function state()
   {
   	return $this->belongsTo('App\Models\admin\State','state_id','state_id');
   } 						  
	public function district()
   {
   	return $this->belongsTo('App\Models\admin\District','district_id','district_id');
   } 

}
