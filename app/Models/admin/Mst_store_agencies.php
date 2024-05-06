<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Mst_store_agencies extends Model
{
    use SoftDeletes;

  	protected $table ="mst_store_agencies";
	  protected $primaryKey = "agency_id";

     protected $fillable = [
    					    'agency_id','agency_name','agency_name_slug','agency_contact_person_name','agency_contact_person_phone_number','agency_contact_number_2','agency_website_link','agency_pincode','agency_primary_address','agency_email_address','agency_username','agency_password','agency_account_status','country_id','state_id','district_id','agency_logo','business_type_id',

    						  ];


   public function country()
   {
   	return $this->belongsTo('App\Models\admin\Country','country_id','country_id');
   } 						  
   public function state()
   {
   	return $this->belongsTo('App\Models\admin\State','state_id','state_id');
   } 						  
	public function district()
   {
   	return $this->belongsTo('App\Models\admin\District','district_id','district_id');
   } 

    public function business_type()
   {
   	return $this->belongsTo('App\Models\admin\Mst_business_types','business_type_id','business_type_id');
   }						  
}
