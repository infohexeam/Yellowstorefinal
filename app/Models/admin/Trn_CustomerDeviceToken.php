<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_CustomerDeviceToken extends Model
{
    protected $table = "trn__customer_device_tokens";
    protected $primaryKey = "customer_device_token_id";

    protected $fillable = [
        'customer_id', 'customer_device_token', 'customer_device_type'

    ];
    public function customer()
    {
        return $this->belongsTo('App\Models\admin\Trn_store_customer', 'customer_id', 'customer_id');
    }
}
