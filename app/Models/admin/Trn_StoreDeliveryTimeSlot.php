<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_StoreDeliveryTimeSlot extends Model
{
    use SoftDeletes;

    protected $primaryKey = "store_delivery_time_slot_id";
    protected $table = "trn__store_delivery_time_slots";

    protected $fillable = [
    					    'store_id','time_start','time_end'
                        ];
                                                   
    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }
}
