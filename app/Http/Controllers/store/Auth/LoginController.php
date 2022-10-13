<?php

namespace App\Http\Controllers\store\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\admin\Mst_store;
use App\Models\admin\Trn_StoreAdmin;
use Crypt;
use App\Models\admin\Trn_StoreWebToken;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */


    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'store/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function showLoginForm()
    {
        return view('store.auth.login');
    }


    public function usrlogin(Request $request)
    {
       

        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            
            $admin = Trn_StoreAdmin::where('store_mobile',$request->store_username)->first();
            $today = Carbon::now()->toDateString();
                     
                      if ($admin) {
                          $cId = $admin->store_id;
                          if($admin->store_account_status == 0)
                          {
                            
                            Auth::guard('store')->logout();
                           return redirect()->back()->with('danger','Profile is inactive ,Contact admin ');

                          }
                          if($today>=$admin->expiry_date)
                          {
                        
                            Auth::guard('store')->logout();
                           return redirect()->back()->with('danger','Profile has been Expired on '.date('d-M-Y',strtotime($admin->expiry_date)).' Contact admin');
                            
                          }
                        
                        if ($admin->store_otp_verify_status==0) {
                            Auth::guard('store')->logout();
                           return redirect('store/registration/otp_verify/view/'.Crypt::encryptString($cId));
                          
                      }
                    
                        

                   
                }
            return $this->sendLoginResponse($request);
        }
        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
        $this->username() => 'exists:trn__store_admins,',
                'password' => 'required|string',
        ], [
                $this->username() . '.exists' => 'The Mobile Number invalid or The Account has been InActive.'
        ]);
    }
    
    public function username()
    {
        return 'store_mobile';
    }


    // protected function credentials(Request $request)
    // {
        
    //     $store = Trn_StoreAdmin::where('store_mobile',$request->store_username)->first();
    //     if ($store) {
    //             return [
    //                 'store_mobile'=>$request->store_username,
    //             'password'=>$request->password,
    //             // 'store_otp_verify_status'=>1, // holded 
    //             ];
    //         }

    //     return $request->only($this->username(), 'password');
    // }

    
    
    protected function credentials(Request $request)
    {
       
        
        $today = Carbon::now()->toDateString();
        $store = Trn_StoreAdmin::where('store_mobile',$request->store_username)->first();
        if ($store) 
        {
        
            if($store->role_id == 0)
            {
                if(Hash::check($request->password, $store->password))
                {
                    if(($store->store_account_status != 0) && ($today <= $store->expiry_date))
                    {
                        return ['store_mobile'=>$request->store_username,'password'=>$request->password];
                    }
                    else if( $store->store_account_status == 1)
                    {
                        return ['store_mobile'=>$request->store_username,'password'=>$request->password];
                    }
                    else
                    {
                        $sadmin = User::where('id','=', 1)->first();
                                if ($store->role_id != 0)
                                {
                                    $getStoreAdmin =   Trn_StoreAdmin::where('store_id','=',$store->store_id)->where('role_id',"=",0)->first();
                                    $phoneNumber = $getStoreAdmin->store_mobile;
                                }else{
                                    $phoneNumber = $sadmin->phone_number;
                                }
                        throw ValidationException::withMessages([
                            $this->username() => 'Store is Inactive. Please contact Admin '.$phoneNumber,
                        ]);
                    }

                }
                else
                {
                    throw ValidationException::withMessages([
                        $this->username() => 'These credentials do not match our records.',
                    ]);
                }
            }
            else
            {
                return ['store_mobile'=>$request->store_username,'password'=>$request->password];
            }
        }
         
        return $request->only($this->username(), 'password');
    }
    

    public function __construct()
    {
        //$this->middleware('guest');
        $this->middleware('guest:store')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('store');
    }

     public function logout(Request $request)
    {
                $store_id  = Auth::guard('store')->user()->store_id;

        Trn_StoreWebToken::where('store_id',$store_id)->delete();

        Auth::guard('store')->logout();
        $cookie = \Cookie::forget('first_time');

        $request->session()->flush();
        $request->session()->regenerate();
        $request->session()->invalidate();

        return redirect('store-login');
    }
    public function redirectStoreLogin(Request $request)
    {
        $mobile=$request->phone_no;
        $sadmin=Trn_StoreAdmin::where('store_mobile',$mobile)->where('role_id',0)->first();
        $sadmin->store_account_status=1;
        $sadmin->store_otp_verify_status=1;
        $sadmin->update();
        return redirect()->route('store.login')->with('status','Otp has been verified.Login Now');
    }


}
