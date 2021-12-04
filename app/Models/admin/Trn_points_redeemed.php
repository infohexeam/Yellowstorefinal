<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_points_redeemed extends Model
{
    protected $primaryKey = "points_redeemed_id";
    protected $table = "trn_points_redeemeds";

   protected $fillable = [
                           'point_in_percentage',
                           'isActive',
               ];
}
