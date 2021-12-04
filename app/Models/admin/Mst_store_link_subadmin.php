<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_store_link_subadmin extends Model
{
    
    protected $primaryKey = "store_link_subadmin_id";

    protected $fillable = [
    					    'store_link_subadmin_id','store_id','subadmin_id',
    						  ];

    public function storeData()
   {
  //  echo "here";die;
    return $this->hasOne('App\Models\admin\Mst_store','store_id');
   } 	
    public function store()
   {
  //  echo "here";die;
    return $this->belongsTo('App\Models\admin\Mst_store','store_id');
   } 	
}
