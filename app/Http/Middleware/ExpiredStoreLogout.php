<?php

namespace App\Http\Middleware;

use App\Models\admin\Trn_StoreAdmin;
use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class ExpiredStoreLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$guard=null)
    {
        if (Auth::guard('store')->check()) {

            $admin = Trn_StoreAdmin::where('store_admin_id',Auth::guard('store')->user()->store_admin_id)->first();
            if($admin)
            {

                if($admin->store_account_status == 0)
                {
                    $sadmin = User::where('id','=', 1)->first();
                    if ($admin->role_id != 0)
                    {
                        $getStoreAdmin =   Trn_StoreAdmin::where('store_id','=',$admin->store_id)->where('role_id',"=",0)->first();
    
                        $phoneNumber = $getStoreAdmin->store_mobile;
                    }else{
                        $phoneNumber = $sadmin->phone_number;
                    }
                $admin->is_logged_in=0;
                $admin->last_active_at=Carbon::now();
                $admin->login_will_expire_at=null;
                $admin->update();
                            
                Auth::guard('store')->logout();
                return redirect()->to('/store-login')->with('danger','Store is inactive,Please Contact admin '.$phoneNumber);

                }
                
                if(Carbon::now()>=$admin->login_will_expire_at)
                {
                    $admin->is_logged_in=0;
                    $admin->last_active_at=Carbon::now();
                    $admin->login_will_expire_at=null;
                    $admin->update();
                    Auth::guard('store')->logout();
                    return redirect()->to('/store-login')->with('danger','Session has been Expired');
                }
                $admin->last_active_at=Carbon::now();
                $admin->login_will_expire_at=Carbon::now()->addHour();
                $admin->update();
                
               // if(where('expiry_time','<=',Carbon::now()->toDateTimeString()))
            }

            $getParentExpiry = Trn_StoreAdmin::where('store_id','=',Auth::guard('store')->user()->store_id)->where('role_id','=',0)->first();
            if($getParentExpiry)
            {
                $today = Carbon::now()->toDateString();
                $parentExpiryDate = $getParentExpiry->expiry_date;
                if($today>$parentExpiryDate)
                {
                $admin = Trn_StoreAdmin::where('store_admin_id',Auth::guard('store')->user()->store_admin_id)->first();
                $admin->is_logged_in=0;
                $admin->last_active_at=Carbon::now();
                $admin->login_will_expire_at=null;
                $admin->update();
                $sadmin = User::where('id','=', 1)->first();
                if ($admin->role_id != 0)
                {
                    $getStoreAdmin =   Trn_StoreAdmin::where('store_id','=',$admin->store_id)->where('role_id',"=",0)->first();

                    $phoneNumber = $getStoreAdmin->store_mobile;
                }else{
                    $phoneNumber = $sadmin->phone_number;
                }
                        
                Auth::guard('store')->logout();
                return redirect()->to('/store-login')->with('danger','Profile has been Expired on '.date('d-M-Y',strtotime($parentExpiryDate)).' Contact admin '.$phoneNumber);
                            
                }
                

            }
            else
            {
                return $next($request);

            }

   
        }
        return $next($request);
    }
}
