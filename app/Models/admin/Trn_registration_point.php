<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_registration_point extends Model
{
    protected $primaryKey = "registration_points_id";
    protected $table = "trn_registration_points";

   protected $fillable = [
                           'registration_point',
                           'valid_from','isActive',
               ];
}
