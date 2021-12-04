<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_referal_point extends Model
{
    protected $primaryKey = "referal_points_id";
    protected $table = "trn_referal_points";

   protected $fillable = [
                           'point',
                           'valid_from','isActive',
               ];
}
