<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_GlobalProductVideo extends Model
{
    protected $table ="trn__global_product_videos";
	protected $primaryKey = "global_product_video_id";

    protected $fillable = [
    					'global_product_id','video_code','platform',
    				
    					  ];


    public function global_product() 
    {
        return $this->belongsTo('App\Models\admin\Mst_GlobalProducts','global_product_id','global_product_id');
    }
}
