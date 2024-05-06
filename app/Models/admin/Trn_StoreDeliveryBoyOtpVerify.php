<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_StoreDeliveryBoyOtpVerify extends Model
{
    protected $table ="trn__store_delivery_boy_otp_verifies";
    protected $primaryKey = "store_delivery_boy_otp_verify_id";

    protected $fillable = [
        'delivery_boy_id','otp_expirytime','otp',
    ];

    public function delivery_boy()
    {
        return $this->belongsTo('App\Models\admin\Mst_delivery_boy','delivery_boy_id','delivery_boy_id');
    } 		
}
