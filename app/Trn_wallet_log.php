<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trn_wallet_log extends Model
{
    protected $table ="trn_wallet_logs";

    protected $primaryKey = "wallet_log_id";


    protected $fillable = [
    					    'store_id',
    					    'order_id',
                            'type',
                            'points_credited',
                            'points_debited'
    						  ];
    public $timestamps=true;

    public function store()
    {
        return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
    } 
}
