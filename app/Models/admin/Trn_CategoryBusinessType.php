<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_CategoryBusinessType extends Model
{
    protected $table = "trn__category_business_types";

    protected $primaryKey = "cbt_id";

    protected $fillable = [
        'category_id',
        'business_type_id',
        'status',
    ];

    public function business_type()
    {
        return $this->belongsTo('App\Models\admin\Mst_business_types', 'business_type_id', 'business_type_id');
    }

    public function categories()
    {
        return $this->belongsTo('App\Models\admin\Mst_categories', 'category_id', 'category_id');
    }
}
