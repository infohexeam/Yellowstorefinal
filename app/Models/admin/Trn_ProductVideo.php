<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_ProductVideo extends Model
{

    protected $primaryKey = "product_video_id";
    protected $table = "trn__product_videos";

    protected $fillable = [
        'product_id', 'product_varient_id', 'link',
        'platform', 'is_active'

    ];



    public function product()
    {
        return $this->belongsTo('App\Models\admin\Mst_store_product', 'product_id', 'product_id');
    }

    public function product_varient()
    {
        return $this->belongsTo('App\Models\admin\Mst_store_product_varient', 'product_varient_id', 'product_varient_id');
    }
}
