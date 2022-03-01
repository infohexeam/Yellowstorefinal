<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Town extends Model
{
    use SoftDeletes;
    protected $table = "mst_towns";

    protected $primaryKey = "town_id";

    protected $fillable = [
        'town_id',
        'town_name',
        'district_id',
        'pin'
    ];


    public function district()
    {
        return $this->belongsTo('App\Models\admin\District', 'district_id', 'district_id');
    }
}
