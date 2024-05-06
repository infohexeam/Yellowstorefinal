<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_StoreBankData extends Model
{
    use SoftDeletes;
    protected $primaryKey = "store_bank_data_id";
    protected $table = "trn__store_bank_data";

    protected $fillable = [
        'store_id',
        'account_number',
        'ifsc',
        'account_holder',
        'email',
        'status',
        'upi_vpa',
        'upi_account_holder',
        'phone',
        'vendor_name',
        'vendor_id',
        'settlement_cycle_id',
    ];


    public function store()
    {
        return $this->belongsTo('App\Models\admin\Mst_store', 'store_id', 'store_id');
    }
}
