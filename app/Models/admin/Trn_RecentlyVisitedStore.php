<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_RecentlyVisitedStore extends Model
{
    protected $primaryKey = "rvs_id";
    protected $table = "trn__recently_visited_stores";

   protected $fillable = [
                           'customer_id',
                           'store_id','visit_count',
               ];

               

               public function customer()
               {
                   return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
               } 

                public function store()
               {
                   return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
               }

}
