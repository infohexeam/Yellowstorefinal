<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_Tax extends Model
{
   use SoftDeletes;
   protected $primaryKey = "tax_id";
   protected $table = "mst__taxes";

   protected $fillable = [
      'tax_value', 'tax_name', 'is_removed'
   ];
}
