<?php

namespace App\Models\admin;
 
use Illuminate\Database\Eloquent\Model;

class Trn_store_setting extends Model
{
    protected $primaryKey = "store_setting_id";
    protected $table = "trn_store_settings";

    protected $fillable = [
    					    'store_id','service_start','service_end','delivery_charge','packing_charge','minimum_order_amount','reduction_percentage'
                        ];
                                                   
    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }

   

    
}
