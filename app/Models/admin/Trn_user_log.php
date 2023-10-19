<?php

namespace App\Models\admin;
 
use Illuminate\Database\Eloquent\Model;

class Trn_user_log extends Model
{
    protected $primaryKey = "user_log_id";
    protected $table = "trn_user_logs";                                             
    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }

   

    
}
