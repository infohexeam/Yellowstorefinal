<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_Video extends Model
{
        use SoftDeletes;
    protected $primaryKey = "video_id";
    protected $table = "mst__videos";

   protected $fillable = [
                           'platform','video_code',
                           'status','visibility','state_id','town_id','district_id','video_image','video_discription'
               ];
               
               
    public function state()
   {
   	return $this->belongsTo('App\Models\admin\State','state_id','state_id');
   }
	public function district()
   {
   	return $this->belongsTo('App\Models\admin\District','district_id','district_id');
   }

   public function town() //town district relation
   {
   	return $this->belongsTo('App\Models\admin\Town','town_id','town_id');
   }
   
}
