<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_StockDetail extends Model
{
    protected $table = "mst__stock_details";
    protected $primaryKey = "stock_detail_id";

    protected $fillable = [
        'store_id',
        'product_id',
        'product_varient_id', 'stock',
        'prev_stock'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\admin\Mst_store_product', 'product_id', 'product_id');
    }

    public function product_varient()
    {
        return $this->belongsTo('App\Models\admin\Mst_store_product_varient', 'product_varient_id', 'product_varient_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Models\admin\Mst_store', 'store_id', 'store_id');
    }
}
