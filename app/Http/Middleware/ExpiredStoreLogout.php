<?php

namespace App\Http\Middleware;

use App\Models\admin\Trn_StoreAdmin;
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
            
            $getParentExpiry = Trn_StoreAdmin::where('store_id','=',Auth::guard('store')->user()->store_id)->where('role_id','=',0)->first();
            if($getParentExpiry)
            {
                $today = Carbon::now()->toDateString();
                $parentExpiryDate = $getParentExpiry->expiry_date;
                if($today>=$parentExpiryDate)
                {
                        
                Auth::guard('store')->logout();
                return redirect()->to('/store-login')->with('danger','Profile has been Expired on '.date('d-M-Y',strtotime($parentExpiryDate)));
                            
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
