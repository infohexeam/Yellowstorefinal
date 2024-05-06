<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_StoreDeviceToken extends Model
{
    protected $table = "trn__store_device_tokens";
    protected $primaryKey = "store_device_token_id";

    protected $fillable = [
        'store_admin_id', 'store_id', 'store_device_token', 'store_device_type', 'store_device_id'

    ];
    public function store()
    {
        return $this->belongsTo('App\Models\admin\Mst_store', 'store_id', 'store_id');
    }
}
