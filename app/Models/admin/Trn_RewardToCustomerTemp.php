<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_RewardToCustomerTemp extends Model
{
    protected $table = "trn__reward_to_customer_temps";
    protected $primaryKey = "reward_to_customer_temp_id";

   protected $fillable = [
                           'customer_mobile_number',
                           'reward_discription','reward_points',
                           'reward_status','added_date',
               ];
}
