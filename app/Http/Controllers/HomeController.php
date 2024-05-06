<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Models\admin\Mst_store_link_subadmin;
use App\Models\admin\Mst_store;
use App\Models\admin\Trn_StoreAdmin;
use DB;
use App\Models\admin\Trn_store_order;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       
       $pageTitle = "Home Page";
       $user_role_id = Auth()->user()->user_role_id;
        $ordersData = Trn_store_order::orderBy('order_id','DESC')->where('order_type','!=','POS')->take(7)->get();

       if($user_role_id == 0)
       {
           $users = Mst_store::select(\DB::raw("COUNT(*) as count"),DB::raw("Month(created_at) as months"))
                    ->whereYear('created_at', date('Y'))
                    ->groupBy(\DB::raw("Month(created_at)"))
                    ->get()->toArray();
                   

            $return = [];
            for($i=1; $i<=12; $i++)
            {
                $months = array_column($users,'months');
                $key = array_search($i, $months);
                if($key !== false)
                {
                $return[] = $users[$key]['count'];
                }else {
                $return[] = 0;
                }
            }
            
        return view('admin.home',compact('pageTitle','return','ordersData'));
       }else
       {
        $user_id = Auth()->user()->id;
        $store = Mst_store_link_subadmin::where('subadmin_id','=',$user_id)->get();
        $count = $store->count();
        return view('subadmin.home',compact('pageTitle','count'));
       }


    }
    public function changePassword()
    {


        $pageTitle = "Update Password";
        $user_id = Auth()->user()->id;

        $admin = User::where('id','=',$user_id)->first();

        return view('admin.masters.password.update_password',compact('pageTitle','admin'));
    }

     public function updatePassword(Request $request, User $admin)
    {

        $user_id = Auth()->user()->id;

        $validator = Validator::make($request->all(),
        [
            'password'         => 'required|same:password_confirmation',

         ],
        [
            'password.required'        => 'Password required',



        ]);
      // $this->uploads($request);
        if(!$validator->fails())
        {
        $data= $request->except('_token');

        $admin = User::Find($user_id);




        if (Hash::check($request->old_password, $admin->password)) 
        {
                            $admin->password       = Hash::make($request->password);
                            $admin->update();
        }
        else
        {
            return redirect()->back()->with('errstatus','Old Password  Incorrect.');

        }
        return redirect()->back()->with('status','Password Updated Successfully.');

    }else
    {

        return redirect()->back()->withErrors($validator)->withInput();
    }

}


   public function updatePasswordStore(Request $request, User $admin)
    {

        

        $validator = Validator::make($request->all(),
        [
            'password'         => 'required|same:password_confirmation|min:8|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/u',

         ],
        [
            'password.required'        => 'Password required',
            'password.regex'=>'Password must include at least one upper case letter, lower case letter, number, and special character'



        ]);
      // $this->uploads($request);
        if(!$validator->fails())
        {
            
            Mst_store::where('store_id',$request->store_id)->update(['password'=>Hash::make($request->password) ]);
            Trn_StoreAdmin::where('store_id',$request->store_id)->where('role_id',0)->update(['password'=>Hash::make($request->password) ]);
            
       
       
            return redirect()->back()->with('status','Password Updated Successfully.');

        }else
        {
    
            return redirect()->back()->withErrors($validator)->withInput();
        }

}


 public function Profile()
    {


        $pageTitle = "Update Profile";
        $user_id = Auth()->user()->id;
        $admin = User::where('id','=',$user_id)->first();
        return view('admin.masters.update_profile',compact('pageTitle','admin'));
    }

     public function updateProfile(Request $request, User $admin)
    {

        $user_id = Auth()->user()->id;
        $admin = User::Find($user_id);

        $validator = Validator::make($request->all(),
        [
            'name'              =>'required',
            'email'              =>'required',
            'phone_number'      => 'required|regex:/^[1-9]\d{9}$/u|digits:10'

         ],
        [

         'name.required'      =>'Username required',
          'email.required'    =>'Email required',
          'phone_number.required' => 'Phone Number is required',


        ]);
      // $this->uploads($request);
        if(!$validator->fails())
        {
        $data= $request->except('_token');


            $admin->name       = $request->name;
            $admin->email       = $request->email;
            $admin->phone_number = $request->phone_number;



       $admin->update();

       return redirect()->back()->with('status',' Profile Updated Successfully.');
    }else
    {

        return redirect()->back()->withErrors($validator)->withInput();
    }

}
}
