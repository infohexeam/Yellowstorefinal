<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
   protected $table ="sys_countries";
	protected $primaryKey = "country_id";

     protected $fillable = [
    					'country_id','country_name','iso','nicename','iso3','numcode','phonecode',

    					 ];

}
