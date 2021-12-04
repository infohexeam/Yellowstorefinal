<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_points_to_rupee extends Model
{
    protected $primaryKey = "points_to_rupees_id";
    protected $table = "trn_points_to_rupees";

   protected $fillable = [
                           'point',
                           'rupee','isActive',
               ];
}
