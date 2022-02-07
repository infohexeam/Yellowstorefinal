<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_ProductVariantAttribute extends Model
{
    //trn__product_variant_attributes
    protected $primaryKey = "variant_attribute_id";
    protected $table = "trn__product_variant_attributes";

    protected $fillable = [
        'product_varient_id',
        'attr_group_id', 'attr_value_id',
    ];


    public function attr_value()
    {
        return $this->belongsTo('App\Models\admin\Mst_attribute_value', 'attr_value_id', 'attr_value_id');
    }

    public function attr_group()
    {
        return $this->belongsTo('App\Models\admin\Mst_attribute_group', 'attr_group_id', 'attr_group_id');
    }

    public function product_varient()
    {
        return $this->belongsTo('App\Models\admin\Mst_store_product_varient', 'product_varient_id', 'product_varient_id');
    }
}
