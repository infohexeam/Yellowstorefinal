<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_attribute_group extends Model
{
    
        use SoftDeletes;

    protected $primaryKey = "attr_group_id";

     protected $fillable = [
    					'attr_group_id','group_name',  
    										  ];

}
