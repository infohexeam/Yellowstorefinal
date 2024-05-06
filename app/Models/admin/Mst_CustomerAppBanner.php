<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_CustomerAppBanner extends Model
{
      use SoftDeletes;
  protected $table ="mst__customer_app_banners";
	protected $primaryKey = "banner_id";

    protected $fillable = [
    					'banner_id','image','town_id','status','store_id','default_status'
    					  ];


    public function town() //town  relation
   {
   	return $this->belongsTo('App\Models\admin\Town','town_id','town_id');
   }
   
   
    public function store() //store   relation
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }
   

}
