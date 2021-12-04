<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_first_order_point extends Model
{
    protected $primaryKey = "first_order_points_id";
    protected $table = "trn_first_order_points";

   protected $fillable = [
                           'registration_point',
                           'valid_from','isActive',
               ];
}
