<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_sub_admin_payment_settlment extends Model
{
    protected $primaryKey = "sub_admin_payment_settlments_id";
    protected $table = "trn_sub_admin_payment_settlments";

   protected $fillable = [

                           'store_id','order_id',
                           'commision_percentage','commision_amount',
                           'sub_admin_commision','subadmin_id'
               ];

public function store()
{
return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
}

public function subadmin()
{
return $this->belongsTo('App\User','App\Models\admin\Trn_sub_admin_payment_settlment','id','subadmin_id');
}

public function order()
{
    return $this->belongsTo('App\Models\admin\Trn_store_order','order_id','order_id');
}

}
