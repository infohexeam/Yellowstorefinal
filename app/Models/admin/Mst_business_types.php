<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Mst_business_types extends Model
{
	use SoftDeletes;

	protected $table = "mst_store_business_types";
	protected $primaryKey = "business_type_id";

	protected $fillable = [
		'business_type_id', 'business_type_name', 'business_type_name_slug', 'business_type_icon', 'business_type_status','is_product_listed'
	];
}
