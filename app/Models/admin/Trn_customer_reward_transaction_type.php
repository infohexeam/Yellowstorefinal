<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_customer_reward_transaction_type extends Model
{
    protected $primaryKey = "transaction_type_id";
    
    protected $fillable = [
    					     'transaction_type_id','transaction_type','transaction_rule','transaction_point_value','transaction_earning_point',

    						  ];
}
