<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_Cart extends Model
{
    protected $table ="trn__carts";

    protected $primaryKey = "cart_id";

    protected $fillable = [
                       'store_id',
                       'customer_id',
                       'product_varient_id',
                       'product_id',
                       'quantity',
                       'remove_status'
                        ];


    public function store()
    {
        return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
    }

   public function customer()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
   }

   public function product_varient()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store_product_varient','product_varient_id','product_varient_id');
   }
   
   public function product()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store_product','product_id','product_id');
   }
}
