<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_configure_points extends Model
{
    protected $primaryKey = "configure_points_id";
    protected $table = "trn_configure_points";

   protected $fillable = [
                           'registraion_points','first_order_points',
                           'referal_points','joiner_points','rupee',
                           'rupee_points','order_amount',
                           'order_points','redeem_percentage','max_redeem_amount'
               ];



}
