<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_RecentlyVisitedProducts extends Model
{
    protected $primaryKey = "rvp_id";
    protected $table = "trn__recently_visited_products";

   protected $fillable = [
                           'customer_id',
                           'store_id','product_id','product_varient_id','visit_count','vendor_id','category_id','sub_category_id'
               ];

               public function product_varient()
               {
                   return $this->belongsTo('App\Models\admin\Mst_store_product_varient','product_varient_id','product_varient_id');
               } 

               public function product()
               {
                   return $this->belongsTo('App\Models\admin\Mst_store_product','product_id','product_id');
               } 

               public function customer()
               {
                   return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
               } 

                public function store()
               {
                   return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
               }

}
