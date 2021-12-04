<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Sys_payment_type extends Model
{
      protected $primaryKey = "payment_type_id";
      protected $table ="sys_payment_type";

      protected $fillable = [
    					    'payment_type_id','payment_type',

    				 ];
}
