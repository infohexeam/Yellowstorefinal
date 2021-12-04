<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_store_payments_tracker extends Model
{
    protected $primaryKey = "store_payments_tracker_id";
    protected $table = "trn_store_payments_tracker";

   protected $fillable = [
                           'store_id','commision_paid',
                           'payment_note','date_of_payment',
               ];

               public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }
}
