<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_subadmin_payments_tracker extends Model
{
    protected $primaryKey = "subadmin_payments_tracker_id";
    protected $table = "trn_subadmin_payments_tracker";

   protected $fillable = [
                           'subadmin_id','commision_paid',
                           'payment_note','date_of_payment',
               ];


    public function subadmin()
   {
   	return $this->belongsTo('App\User','id','subadmin_id');
   }
}
