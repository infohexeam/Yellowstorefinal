<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
        use SoftDeletes;

     protected $table ="mst_districts";
	 protected $primaryKey = "district_id";

     protected $fillable = [
    					'district_id','district_name','state_id',
    					 ];

    public function state()
	 {
	 	 return $this->belongsTo('App\Models\admin\State','state_id','state_id');
	 }
}
