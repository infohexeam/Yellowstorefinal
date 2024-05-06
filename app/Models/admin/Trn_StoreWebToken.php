<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_StoreWebToken extends Model
{
    protected $table = "trn__store_web_tokens";

    protected $primaryKey = "store_web_token_id";


    protected $fillable = [
        'store_admin_id',
        'store_id',
        'store_web_token',
    ];
}
