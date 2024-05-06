<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_store_link_delivery_boy extends Model
{
    protected $primaryKey = "store_link_delivery_boy_id";

    protected $fillable = [
    					    'store_link_delivery_boy_id','store_id','delivery_boy_id',
    						  ];

    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   } 						  
   public function delivery_boy()
   {
   	return $this->belongsTo('App\Models\admin\Mst_delivery_boy','delivery_boy_id','delivery_boy_id');
   }

}
