<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_MostVisitedProductsByCustomer extends Model
{
    protected $table = "trn__most_visited_products_by_customers";
    protected $primaryKey = "mvpbc_id";

   protected $fillable = [
                            'customer_id',
                            'store_id',
                            'product_id',
                           'product_varient_id','visit_count',
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
}
