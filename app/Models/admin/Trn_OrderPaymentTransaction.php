<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_OrderPaymentTransaction extends Model
{
    protected $primaryKey = "opt_id";
    protected $table = "trn__order_payment_transactions";

    protected $fillable = [
        'order_id', 'paymentMode', 'PGOrderId', 'txTime', 'referenceId', 'txMsg', 'orderAmount', 'txStatus', 'payment_mode_flag', 'isFullPaymentToAdmin'
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\admin\Trn_store_order', 'order_id', 'order_id');
    }
}
