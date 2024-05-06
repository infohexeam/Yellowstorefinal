<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_Subadmin_Detail extends Model
{
    protected $primaryKey = 'subadmin_details_id';
    protected $table = "mst_subadmin_details";

    protected $fillable = [


        'subadmin_id',
        'subadmin_address',
         'subadmin_commision_amount',
         'subadmin_commision_percentage',
         'phone',
          'country_id',
                            'state_id',
                            'district_id',
                            'town_id',

    ];

 	public function subadmins()
   {
   	return $this->belongsTo('App\User','subadmin_id','id');
   }


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

   public function town() //town district relation
   {
   	return $this->belongsTo('App\Models\admin\Town','town_id','town_id');
   }

}
