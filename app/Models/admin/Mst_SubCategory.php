<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_SubCategory extends Model
{
    use SoftDeletes;

    protected $table ="mst__sub_categories";
  protected $primaryKey = "sub_category_id";

   protected $fillable = [
                      'category_id','business_type_id','sub_category_name',
                      'sub_category_name_slug','sub_category_icon',
                      'sub_category_description','sub_category_status',
    
                          ];

  
     public function categories()
   {
        return $this->belongsTo('App\Models\admin\Mst_categories','category_id','category_id');
   }
     public function business_type()
   {
        return $this->belongsTo('App\Models\admin\Mst_business_types','business_type_id','business_type_id');
   }
}
