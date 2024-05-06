<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Sys_store_order_status extends Model
{
     protected $primaryKey = "status_id";
     protected $table = "sys_store_order_status";

    protected $fillable = [
    					    'status_id','status',

				];
}
