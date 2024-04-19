<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_GlobalProducts extends Model
{
    
        use SoftDeletes;

    protected $table ="mst__global_products";
	protected $primaryKey = "global_product_id";

    protected $fillable = [
    					'product_name','product_name_slug','product_description',
    					'regular_price','sale_price','tax_id',
    					'min_stock','product_code','business_type_id',
    					'color_id','product_brand','attr_group_id',
    					'attr_value_id','product_cat_id','vendor_id',
    					'product_base_image','created_date','created_by','sub_category_id','isConvertedFromProducts','supply_type'
    					  ];


    public function tax() 
    {
        return $this->belongsTo('App\Models\admin\Mst_Tax','tax_id','tax_id');
    }

    public function business_type() 
    {
        return $this->belongsTo('App\Models\admin\Mst_business_types','business_type_id','business_type_id')->withTrashed();
    }

    public function color() 
    {
        return $this->belongsTo('App\Models\admin\Mst_attribute_value','color_id','attr_value_id');
    }

    public function attr_group() 
    {
        return $this->belongsTo('App\Models\admin\Mst_attribute_group','attr_group_id','attr_group_id');
    }

    public function attr_value() 
    {
        return $this->belongsTo('App\Models\admin\Mst_attribute_value','attr_value_id','attr_value_id');
    }

    public function product_cat() 
    {
        return $this->belongsTo('App\Models\admin\Mst_categories','product_cat_id','category_id')->withTrashed();
    }

    public function product_subcat() 
    {
        return $this->belongsTo('App\Models\admin\Mst_SubCategory','sub_category_id','sub_category_id')->withTrashed();
    }

    public function vendor() 
    {
        return $this->belongsTo('App\Models\admin\Mst_store_agencies','vendor_id','agency_id');
    }


}
