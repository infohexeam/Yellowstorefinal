<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_StoreTimeSlot extends Model
{
    protected $primaryKey = "store_time_slot_id";
    protected $table = "trn__store_time_slots";

    protected $fillable = [
    					    'store_id','day','time_start','time_end'
                        ];
                                                   
    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }

}
