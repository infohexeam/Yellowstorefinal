<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Sys_IssueType extends Model
{
    protected $primaryKey = "issue_type_id";
    protected $table ="sys__issue_types";

    protected $fillable = [
                          'issue_type',
                   ];
}
