<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_TaxSplitUp extends Model
{
    protected $primaryKey = "tax_split_up_id";
    protected $table = "trn__tax_split_ups";

   protected $fillable = [
                           'tax_id','split_tax_name',
                           'split_tax_value',
               ];


               public function tax()
               {
                   return $this->belongsTo('App\Models\admin\Mst_Tax','tax_id','tax_id');
               }
}
