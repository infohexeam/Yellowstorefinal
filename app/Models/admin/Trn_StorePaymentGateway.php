<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_StorePaymentGateway extends Model
{
    protected $table = "trn__store_payment_gateways";
    protected $primaryKey = "spg_id";

    protected $fillable = [
        'store_id', 'payment_gateway_id', 'api_key', 'api_secret_key', 'pg_status'

    ];
    public function store()
    {
        return $this->belongsTo('App\Models\admin\Mst_store', 'store_id', 'store_id');
    }

    public function payment_gateway()
    {
        return $this->belongsTo('App\Models\admin\Sys_PaymentGateway', 'payment_gateway_id', 'payment_gateway_id');
    }
}
