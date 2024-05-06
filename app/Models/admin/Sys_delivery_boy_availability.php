<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Sys_delivery_boy_availability extends Model
{
   
	 protected $primaryKey = "availability_id";

     protected $fillable = [
    					'availability_id','availabilable_days','availabilable_time','active_flag',
    					 ];
}
