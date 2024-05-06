<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_DeliveryBoyDeviceToken extends Model
{
    protected $table = "trn__delivery_boy_device_tokens";
    protected $primaryKey = "delivery_boy_device_token_id";

    protected $fillable = [
        'delivery_boy_id',
        'dboy_device_token',
        'dboy_device_type'
    ];

    public function delivery_boy()
    {
        return $this->belongsTo('App\Models\admin\Mst_delivery_boy', 'delivery_boy_id', 'delivery_boy_id');
    }
}
