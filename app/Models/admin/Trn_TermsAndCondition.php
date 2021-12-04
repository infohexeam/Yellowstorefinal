<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_TermsAndCondition extends Model
{
    protected $primaryKey = "tc_id";
    protected $table = "trn__terms_and_conditions";

    protected $fillable = [
                    'terms_and_condition'
               ];
}
