<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_DeliveryBoyLocation extends Model
{
    protected $table = "trn__delivery_boy_locations";
    protected $primaryKey = "dbl_id";

    protected $fillable = [
        'delivery_boy_id',
        'latitude',
        'longitude'
    ];

    public function delivery_boy()
    {
        return $this->belongsTo('App\Models\admin\Mst_delivery_boy', 'delivery_boy_id', 'delivery_boy_id');
    }
}
