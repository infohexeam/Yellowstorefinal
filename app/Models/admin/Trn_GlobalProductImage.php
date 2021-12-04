<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_GlobalProductImage extends Model
{
    protected $table ="trn__global_product_images";
	protected $primaryKey = "global_product_image_id";

    protected $fillable = [
    					'global_product_id','image_name',
    				
    					  ];


    public function global_product() 
    {
        return $this->belongsTo('App\Models\admin\Mst_GlobalProducts','global_product_id','global_product_id');
    }
}
