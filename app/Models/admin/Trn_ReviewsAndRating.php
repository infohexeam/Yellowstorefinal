<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_ReviewsAndRating extends Model
{
    protected $table = "trn__reviews_and_ratings";
    protected $primaryKey = "reviews_id";

   protected $fillable = [
                           'customer_id',
                           'store_id',
                           'product_id',
                           'product_varient_id',
                           'rating',
                           'review',
                           'reviews_date',
               ];


               public function store()
               {
                   return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
               }
              
                 public function product_varient()
               {
                   return $this->belongsTo('App\Models\admin\Mst_store_product_varient','product_varient_id','product_varient_id');
                 // return $this->product_varient_id;
               }
            
               public function product()
               {
               return $this->belongsTo('App\Models\admin\Mst_store_product_varient','product_id','product_id');
                }
            
                public function customer()
               {
                   return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
               }
            
}
