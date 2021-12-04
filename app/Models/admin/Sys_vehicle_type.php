<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sys_vehicle_type extends Model
{
        use SoftDeletes;
  protected $primaryKey = "vehicle_type_id";

    protected $fillable = [
    					    'vehicle_type_id','vehicle_type_name',

            ];

}
