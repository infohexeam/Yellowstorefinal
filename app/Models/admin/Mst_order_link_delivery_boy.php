<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_order_link_delivery_boy extends Model
{
   protected $primaryKey = "order_link_id";

    protected $fillable = [
    	'order_link_id', 'order_id','delivery_boy_id',

							];
}
