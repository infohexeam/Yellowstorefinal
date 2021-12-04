<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_Issues extends Model
{
        use SoftDeletes;

    protected $table ="mst__issues";

    protected $primaryKey = "issue_id";


    protected $fillable = [
    					    'issue',
    					    'issue_type_id',
    						  ];

    public function issue_type()
    {
        return $this->belongsTo('App\Models\admin\Sys_IssueType','issue_type_id','issue_type_id');
    }                      
}
