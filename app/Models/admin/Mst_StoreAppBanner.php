<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_StoreAppBanner extends Model
{
      use SoftDeletes;
  protected $table ="mst__store_app_banners";
	protected $primaryKey = "banner_id";

    protected $fillable = [
    					'banner_id','image','town_id','status'
    					  ];

    public function town() //town  relation
   {
   	return $this->belongsTo('App\Models\admin\Town','town_id','town_id');
   }
}
