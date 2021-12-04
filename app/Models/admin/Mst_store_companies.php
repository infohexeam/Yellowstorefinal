<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Mst_store_companies extends Model
{
    use SoftDeletes;

  	protected $table ="mst_store_companies";
	protected $primaryKey = "company_id";

    protected $fillable = [
    					    'company_id','company_name','company_name_slug','company_contact_person_name','company_contact_person_phone_number','company_contact_number_2','company_website_link','company_pincode','company_primary_address','company_email_address','company_username','company_password','company_account_status','country_id','state_id','district_id','company_logo','business_type_id','deleted_at',

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