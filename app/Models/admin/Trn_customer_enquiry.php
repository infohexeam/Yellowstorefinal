<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_customer_enquiry extends Model
{
    protected $table="trn_customer_enquiry";
    
    protected $primaryKey = "enquiry_id";
   protected $fillable=['customer_id','product_varient_id','visited_date','store_id'];
    public $timestamps=true;

 public function customer()
   {
   	return $this->belongsTo('App\Models\admin\Trn_store_customer','customer_id','customer_id');
   }

   public function store()
   {
    return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }      
   public function varient()
   {
    return $this->belongsTo('App\Models\admin\Mst_store_product_varient','product_varient_id','product_varient_id');
   }						  
}
