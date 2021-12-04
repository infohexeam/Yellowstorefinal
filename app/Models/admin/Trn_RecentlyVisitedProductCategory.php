<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_RecentlyVisitedProductCategory extends Model
{
    protected $primaryKey = "rvpc_id";
    protected $table = "trn__recently_visited_product_categories";

   protected $fillable = [
                           'customer_id','store_id','category_id','visit_count'
               ];

               public function customer()
               {
                   return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
               } 

                public function store()
               {
                   return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
               }

              
                public function category()
               {
                   return $this->belongsTo('App\Models\admin\Mst_categories','category_id','category_id');
               }
}
