<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_dispute extends Model
{
    protected $table ="mst_disputes";

    protected $primaryKey = "dispute_id";


    protected $fillable = [
    					    'order_id',
                            'issue_id',
                            'item_ids',
                            'order_number',
                            'customer_id',
                            'product_id',
                            'discription',
                            'dispute_date',
                            'store_id',
                            'subadmin_id',
                            'dispute_status',
                            'order_item_id',
                            'store_response',
    						  ];


 	public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }

   public function issues()
   {
   	return $this->belongsTo('App\Models\admin\Mst_Issues','issue_id','issue_id');
   }

   public function subadmin()
   {
   	return $this->belongsTo('App\User','subadmin_id','id');
   }

   public function order()
   {
    return $this->belongsTo('App\Models\admin\Trn_store_order','order_id','order_id');
}


}
