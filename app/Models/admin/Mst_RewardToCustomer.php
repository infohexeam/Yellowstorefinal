<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_RewardToCustomer extends Model
{
        use SoftDeletes;

    protected $table = "mst__reward_to_customers";
    protected $primaryKey = "reward_to_customer_id";

   protected $fillable = [
                            'user_id',
                            'customer_mobile_number',
                           'reward_discription','reward_points',
                           'reward_status','added_date',
               ];

    public function admin()
   {
   	return $this->belongsTo('App\User','user_id','id');
   }

}
