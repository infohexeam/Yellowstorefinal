<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_OrderSplitPayments extends Model
{
    protected $primaryKey = "osp_id";
    protected $table = "trn__order_split_payments";

    protected $fillable = [
        'opt_id', 'order_id', 'splitAmount',
        'serviceCharge', 'serviceTax', 'splitServiceCharge',
        'splitServiceTax', 'settlementAmount','settlementId',
        'settlementEligibilityDate', 'paymentRole'
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\admin\Trn_store_order', 'order_id', 'order_id');
    }
    public function opt()
    {
        return $this->belongsTo('App\Models\admin\Trn_OrderPaymentTransaction', 'opt_id', 'opt_id');
    }
}
