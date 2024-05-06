<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sys_PaymentGateway extends Model
{
    use SoftDeletes;
    protected $primaryKey = "payment_gateway_id";
    protected $table = "sys__payment_gateways";

    protected $fillable = [
        'payment_gateway',

    ];
}
