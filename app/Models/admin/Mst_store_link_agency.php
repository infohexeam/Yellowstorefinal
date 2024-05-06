<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_store_link_agency extends Model
{
    protected $table ="mst_store_link_agency";
	   protected $primaryKey = "link_id";

    protected $fillable = [
    					    'link_id','store_id','agency_id',
    						  ];
 
    public function store_data()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id');
   } 						  
   public function agency()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store_agencies','agency_id','agency_id');
   }

}
