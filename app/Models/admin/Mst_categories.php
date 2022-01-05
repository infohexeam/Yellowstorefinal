<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_categories extends Model
{
    use SoftDeletes;

  	protected $table ="mst_store_categories";
	protected $primaryKey = "category_id";

     protected $fillable = [
    					'category_id ','parent_id','business_type_id','category_name','category_name_slug','category_icon','category_description','category_status',
	  
	  					  ];

	
	   public function categories()
	 {
	 	 return $this->belongsTo('App\Models\admin\Mst_categories','parent_id','category_id');
	 }
	   public function business_type()
	 {
	 	 return $this->belongsTo('App\Models\admin\Mst_business_types','business_type_id','business_type_id');
	 }

	 public function business_types()
	 {
		 return $this->hasMany('App\Models\admin\Trn_CategoryBusinessType', 'category_id', 'category_id');
	 }
}
