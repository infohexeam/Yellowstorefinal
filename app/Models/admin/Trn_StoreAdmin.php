<?php

namespace App\Models\admin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VendorResetPasswordNotification;
use Laravel\Passport\HasApiTokens;

class Trn_StoreAdmin extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    
    protected $guard =  "store";
    protected $primaryKey = "store_admin_id";
    protected $table = "trn__store_admins";

    protected $fillable = [
        'store_id','admin_name','email','username','expiry_date',
        'store_mobile','role_id','store_account_status','store_otp_verify_status','password','subadmin_id',
                        ];
                                                   
    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }
    public function AauthAcessToken()
    {
      return $this->hasMany('\App\Models\OauthAccessToken','user_id','store_id');
    }
}
