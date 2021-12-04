<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_store_images extends Model
{
   protected $table ="mst_store_images";
	protected $primaryKey = "store_image_id";

    protected $fillable = [
    					'store_image_id','store_image','store_id','default_image',
    					  ];



	public function store()
	{
		return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
	}

}
