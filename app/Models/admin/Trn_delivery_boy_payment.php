<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_delivery_boy_payment extends Model
{
    protected $primaryKey = "delivery_boy_payment_id";
    protected $table = "trn_delivery_boy_payments";

   protected $fillable = [
                           'delivery_boy_id','commision_paid',
                           'payment_note','date_of_payment',
               ];
}
