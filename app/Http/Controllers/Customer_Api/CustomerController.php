<?php

namespace App\Http\Controllers\Customer_Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Response;
use Image;
use DB;
use Hash;
use Carbon\Carbon;
use Crypt;
use Mail;
use PDF;
use Auth;

use App\Models\admin\Trn_store_customer;
use App\Models\admin\Trn_store_customer_otp_verify;
use App\Models\admin\Trn_customerAddress;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_customer_reward_transaction_type;

use App\Models\admin\Trn_CustomerDeviceToken;
use App\Models\admin\Trn_StoreDeviceToken;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Mst_RewardToCustomer;

use App\Models\admin\Country;
use App\Models\admin\Mst_store;
use App\Models\admin\State;
use App\Trn_store_referrals;

class CustomerController extends Controller
{
    public function logout(Request $request)
    {

        if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {

            $accessToken = auth()->user()->token();
            $token = $request->user()->tokens->find($accessToken);
            $token->revoke();
            Trn_CustomerDeviceToken::where('customer_id',$request->customer_id)->delete();

            $data['status'] = 1;
            $data['message'] = "Success";
        } else {
            $data['status'] = 0;
            $data['message'] = "Failed";
        }
        return response($data);

        // $accessToken = auth()->user()->token();
        // $token = $request->user()->tokens->find($accessToken);
        // $token->revoke();
        // $data['status'] = 1;
        // $data['message'] = "Success";
        // return response($data);
    }

    public function loginCustomer(Request $request)
    {
        $data = array();
        try {
            $phone = $request->input('customer_mobile_number');
            $passChk = $request->input('password');
            $devType = $request->input('device_type');
            $devToken = $request->input('device_token');

            $validator = Validator::make(
                $request->all(),
                [
                    'customer_mobile_number' => 'required',
                    'password' => 'required',
                    // 'device_type' => 'required',
                    // 'device_token' => 'required',
                ],
                [
                    'customer_mobile_number.required' => "Customer Mobile Number is required",
                    'password.required' => "Password is required",
                    // 'device_type.required' => "Device Type is required",
                    // 'device_toke.required' => "Device Token is required",
                ]
            );
            // dd($validator);
            if (!$validator->fails()) {
                $custCheck = Trn_store_customer::where('customer_mobile_number', '=', $phone)->first();
                $today = Carbon::now()->toDateString();
                if ($custCheck) {

                    if (Hash::check($passChk, $custCheck->password)) {
                        if ($custCheck->customer_profile_status != 0) {
                            if ($custCheck->customer_otp_verify_status != 0) {

                                if (Auth::guard('customer')->attempt(['customer_mobile_number' => request('customer_mobile_number'), 'password' => request('password')])) {
                                    $user = Trn_store_customer::find(auth()->guard('customer')->user()->customer_id);

                                    if (isset($request->device_token) && isset($request->device_type)) {
                                        Trn_CustomerDeviceToken::where('customer_id', auth()->guard('customer')->user()->customer_id)
                                            //  ->where('customer_device_token',$request->device_token)
                                            ->delete();

                                        $cdt = new Trn_CustomerDeviceToken;
                                        $cdt->customer_id = auth()->guard('customer')->user()->customer_id;
                                        $cdt->customer_device_token = $request->device_token;
                                        $cdt->customer_device_type = $request->device_type;
                                        $cdt->save();
                                    }



                                    $data['token'] =  $user->createToken('authToken', ['customer'])->accessToken;
                                    $data['status'] = 1;
                                    $data['message'] = "Login Success";

                                    $data['customer_id'] = $user->customer_id;
                                    $data['customer_first_name'] = $user->customer_first_name . " " . $user->customer_last_name;
                                    $data['customer_mobile_number'] = $user->customer_mobile_number;
                                    $data['referral_id'] = $user->referral_id;
                                    /*********Referral Insertion */
                                    if($request->referral_id)
                                    {
                                    if($request->store_referral_number)
                                    {
                                    if($user->referral_id == $request->referral_id)
                                    {
                                        $data['status'] = 0;
                                        $data['message'] = "Invalid Reference.Cannot initiate a reference created by yourselves";
                                        return response($data);

                                    }
                                    
                                    
                                    $check_reference_exists=Trn_store_referrals::where('joined_by_number',$user->referral_id)->where('refered_by_number',$request->referral_id)->where('store_referral_number',$request->store_referral_number)->first();
                                    if($check_reference_exists==NULL)  
                                    {
                                        //$request->store_referral_number
                                        $joiner_points=0;
                                        $referal_points=0;
                                        $fop=0;
                                        $store_id=0;
                                        
                                        $str=Mst_store::where('store_referral_id',$request->store_referral_number)->first();
                                        if($str)
                                        {
                                        if(is_null($str->store_referral_id))
                                        {
                                            $st_uid=$str->store_id;
                                            $cnfg=Trn_configure_points::where('store_id',$st_uid)->first();
                                            if($cnfg)
                                            {
                                                $joiner_points=$cnfg->joiner_points;
                                                $referal_points=$cnfg->referal_points;
                                                $fop=$cnfg->first_order_points;
                                                $store_id=$str->store_id;
                        
                                            }
                                            else
                                            {
                                                // $data['status'] = 0;
                                                // $data['message'] = "No configure points added to the store";
                                                // return response($data);
                        
                                            }
                                           
                        
                                        }
                                        else
                                        {
                                            $st_uid=$str->store_referral_id;
                                            $st=Mst_store::where('store_referral_id',$st_uid)->first();
                                            if($st)
                                            {
                                               // dd(2);
                                            $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                                            if($cnfg)
                                            {
                                            $joiner_points=$cnfg->joiner_points;
                                            $referal_points=$cnfg->referal_points;
                                            $fop=$cnfg->first_order_points;
                                            $store_id=$st->store_id;
                        
                                            }
                                            else
                                            {
                                                // $data['status'] = 0;
                                                // $data['message'] = "No configure points added to the store";
                                                // return response($data);
                        
                                            }
                                            
                        
                                            }
                        
                                        }
                                    }
                                    else
                                    {
                                        $st=Mst_store::where('store_id',$request->store_referral_number)->first();
                                        $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                                        if($cnfg)
                                        {
                                        $joiner_points=$cnfg->joiner_points;
                                        $referal_points=$cnfg->referal_points;
                                        $fop=$cnfg->first_order_points;
                                        $store_id=$st->store_id;
                        
                                        }
                                        else
                                        {
                                        //     $data['status'] = 0;
                                        //     $data['message'] = "No configure points added to the store";
                                        //     return response($data);
                        
                                        }
                        
                                    }
                                        // if($str)
                                        // {
                                        //     //dd(1);
                                        //     $cnfg=Trn_configure_points::where('store_id',$str->store_id)->first();
                                        //     $joiner_points=$cnfg->joiner_points;
                                        //     $referal_points=$cnfg->referal_points;
                                        //     $fop=$cnfg->first_order_points;
                                        //     $store_id=$str->store_id;
                        
                                            
                        
                                        // }
                                        // else
                                        // {
                                        //     $st=Mst_store::where('store_id',$request->store_referral_number)->first();
                                        //     if($st)
                                        //     {
                                        //        // dd(2);
                                        //     $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                                        //     $joiner_points=$cnfg->joiner_points;
                                        //     $referal_points=$cnfg->referal_points;
                                        //     $fop=$cnfg->first_order_points;
                                        //     $store_id=$st->store_id;
                        
                                        //     }
                                        //     //dd(3);
                        
                                        // }
                                        $store_referral=new Trn_store_referrals();
                                        $store_referral->store_referral_number=$request->store_referral_number;
                                        $store_referral->store_id=$store_id;
                                        $store_referral->refered_by_id=$request->referred_customer_id??0;
                                        $store_referral->refered_by_number=$request->referral_id;
                                        $store_referral->joined_by_id=$user->customer_id;
                                        $store_referral->joined_by_number=$user->referral_id;
                                        $store_referral->reference_status=0;
                                        $store_referral->joiner_points=$joiner_points;
                                        $store_referral->referral_points=$referal_points;
                                        $store_referral->fop=$fop;
                                        $store_referral->save();
                                        $data['status'] = 1;
                                        $data['message'] = "Success";
                                        // return response($data);
                        
                                    }
                                    else
                                    {
                                        // $data['status'] = 0;
                                        // $data['message'] = "Failed...Referral was done previously";
                                        // return response($data);
                        
                                    }
                                }
                            }
                                    

                                    /********************************8 */



                                    // customer reward 
                                    $rewardCount = Trn_customer_reward::where('customer_id', $user->customer_id)->count();
                                    if ($rewardCount <= 1) {
                                        //     $rewards = Mst_RewardToCustomer::where('customer_mobile_number', $user->customer_mobile_number)->get();
                                        //     Mst_RewardToCustomer::where('customer_mobile_number', $user->customer_mobile_number)->delete();
                                        //     foreach ($rewards as $r) {
                                        //         $cr1 = new Trn_customer_reward;
                                        //         $cr1->transaction_type_id = 0;
                                        //         $cr1->reward_points_earned = $r->reward_points;
                                        //         $cr1->customer_id = $user->customer_id;
                                        //         $cr1->order_id = null;
                                        //         $cr1->reward_approved_date = $r->added_date;
                                        //         $cr1->reward_point_expire_date = $r->added_date;
                                        //         $cr1->reward_point_status = 1;
                                        //         $cr1->discription = $r->reward_discription;
                                        //         $cr1->save();
                                        //     }
                                        // } else {
                                        // $customer_id  = $user->customer_id;
                                        // $configPoint = Trn_configure_points::find(1);
                                        // $orderAmount  = $configPoint->order_amount;
                                        // $orderPoint  = $configPoint->order_points;

                                        // $cr = new Trn_customer_reward;
                                        // $cr->transaction_type_id = 0;
                                        // $cr->reward_points_earned = $configPoint->registraion_points;
                                        // $cr->customer_id = $customer_id;
                                        // $cr->order_id = null;
                                        // $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
                                        // $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                                        // $cr->reward_point_status = 1;
                                        // $cr->discription = 'Registration points';
                                        // if ($cr->save()) {
                                        //     $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $customer_id)->get();
                                        //     foreach ($customerDevice as $cd) {
                                        //         $title = 'Registration points credited';
                                        //         $body = $configPoint->registraion_points . ' points credited to your wallet..';
                                        //         //   $body = 'Registration points credited successully..';
                                        //         $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body);
                                        //     }
                                        // }
                                    }
                                }
                            } else {
                                $customer_otp=rand(100000,999999);
                                $otp_verify=Trn_store_customer_otp_verify::where('customer_id','=',$custCheck->customer_id)->first();
                                $customer_otp_expirytime = Carbon::now()->addMinute(10);
                                $otp_verify->customer_id                 = $custCheck->customer_id;
                                $otp_verify->customer_otp_expirytime     = $customer_otp_expirytime;
                                $otp_verify->customer_otp                 = $customer_otp;
                                $otp_verify->save();
                                $res=Helper::sendOtp($phone,$customer_otp,1);
                                $data['otp_session_id']=$res['session_id'];
                                $data['status'] = 2;
                                $data['message'] = "OTP not verified";
                                $data['otp']=$customer_otp;
                                $data['customer_id'] = $custCheck->customer_id;
                            }
                        } else {


                            $data['customer_id'] = $custCheck->customer_id;

                            $data['status'] = 4;
                            $data['message'] = "Profile blocked";
                        }
                    } else {
                        $data['status'] = 3;
                        $data['message'] = "Mobile Number or Password is Invalid";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Mobile number!";
                }
            } else {
                $data['errors'] = $validator->errors();
                $data['message'] = "Login Failed";
            }

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }





    public function mobUniqueCheck(Request $request)
    {
        $data = array();
        try {
            if ($request->customer_mobile_number) {

                if (Trn_store_customer::where('customer_otp_verify_status', 0)->where("customer_mobile_number", '=', $request->customer_mobile_number)->count() > 0) {
                    Trn_store_customer::where("customer_mobile_number", '=', $request->customer_mobile_number)->delete();
                }

                if (Trn_store_customer::where("customer_mobile_number", '=', $request->customer_mobile_number)->first()) {
                    $data['status'] = 0;
                    $data['message'] = "Mobile number already in use";
                } else {
                    $data['status'] = 1;
                    $data['message'] = "Mobile number accepted";
                }
            } else {
                $data['status'] = 2;
                $data['message'] = "Mobile number cannot be empty";
            }

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }



    public function emailUniqueCheck(Request $request)
    {
        $data = array();
        try {
            if ($request->customer_email) {

                $validator = Validator::make(
                    $request->all(),
                    [
                        //    'customer_email'          => 'email',
                    ]
                );

                if (!$validator->fails()) {
                    if (Trn_store_customer::where("customer_email", '=', $request->customer_email)->first()) {
                        $data['status'] = 0;
                        $data['message'] = "Email already in use";
                    } else {
                        $data['status'] = 1;
                        $data['message'] = "Email accepted";
                    }
                } else {
                    $data['status'] = 3;
                    $data['message'] = "Email invalid";
                }
            } else {
                $data['status'] = 2;
                $data['message'] = "Email cannot be empty";
            }

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function saveCustomer(Request $request, Trn_store_customer $customer, Trn_store_customer_otp_verify $otp_verify)
    {
        $data = array();
        try {

            $validator = Helper::validateCustomer($request->all());
            if (!$validator->fails()) {
                $cusName = explode(' ', $request->customer_name, 2);

                $customer->customer_first_name            = $cusName[0];
                $customer->customer_last_name            = @$cusName[1];
                $customer->customer_email   = $request->customer_email;
                $customer->customer_mobile_number   = $request->customer_mobile_number;
                $customer->password              = Hash::make($request->password);
                $customer->customer_profile_status       = 1;
                $customer->customer_otp_verify_status       = 0;

                $customer->latitude   = $request->latitude;
                $customer->longitude   = $request->longitude;
                $customer->customer_pincode   = $request->pincode;
                $customer->place   = $request->place;

                if (isset($request->referral_id)) {
                    $refById = Trn_store_customer::select('customer_id')->where('referral_id', $request->referral_id)->first();
                    //dd($refById);
                    $customer->referred_by   = @$refById->customer_id;
                }

                $customer->save();

                $customer_id = DB::getPdo()->lastInsertId();
                
                if($request->customer_mobile_number )


                $customer_otp =  rand(100000, 999999);
                $stt = Str::of($request->customer_name)->slug('-');
                $st4 = substr($stt, 0, 4);
                $stringRefer = $customer_id . $st4 . $customer_otp;
                Trn_store_customer::where('customer_id', $customer_id)->update(['referral_id' => $stringRefer]);


                $customer_otp_expirytime = Carbon::now()->addMinute(10);

                $otp_verify->customer_id                 = $customer_id;
                $otp_verify->customer_otp_expirytime     = $customer_otp_expirytime;
                $otp_verify->customer_otp                 = $customer_otp;
                $otp_verify->save();
                //***********************Referal insertion****************************/
                if (isset($request->referral_id)) {
                if (isset($customer_id) && Trn_store_customer::find($customer_id)) {
                    // if($request->joined_customer_ref_number==$request->referred_customer_ref_number)
                    // {
                    //     $data['status'] = 0;
                    //     $data['message'] = "Invalid Reference.Cannot initiate a reference created by yourselves";
                    //     return response($data);
    
                    // }
                if($request->store_referral_number)
                {
                $check_reference_exists=Trn_store_referrals::where('joined_by_number',$stringRefer)->where('refered_by_number',$request->referral_id)->where('store_referral_number',$request->store_referral_number)->first();
                if($check_reference_exists==NULL)  
                {
                    //$request->store_referral_number
                    $joiner_points=0;
                    $referal_points=0;
                    $fop=0;
                    $store_id=0;
                    
                    $str=Mst_store::where('store_referral_id',$request->store_referral_number)->first();
                    if($str)
                    {
                    if(is_null($str->store_referral_id))
                    {
                        $st_uid=$str->store_id;
                        $cnfg=Trn_configure_points::where('store_id',$st_uid)->first();
                        if($cnfg)
                        {
                            $joiner_points=$cnfg->joiner_points;
                            $referal_points=$cnfg->referal_points;
                            $fop=$cnfg->first_order_points;
                            $store_id=$str->store_id;
    
                        }
                        else
                        {
                            // $data['status'] = 0;
                            // $data['message'] = "No configure points added to the store";
                            // return response($data);
    
                        }
                       
    
                    }
                    else
                    {
                        $st_uid=$str->store_referral_id;
                        $st=Mst_store::where('store_referral_id',$st_uid)->first();
                        if($st)
                        {
                           // dd(2);
                        $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                        if($cnfg)
                        {
                        $joiner_points=$cnfg->joiner_points;
                        $referal_points=$cnfg->referal_points;
                        $fop=$cnfg->first_order_points;
                        $store_id=$st->store_id;
    
                        }
                        else
                        {
                            // $data['status'] = 0;
                            // $data['message'] = "No configure points added to the store";
                            // return response($data);
    
                        }
                        
    
                        }
    
                    }
                }
                else
                {
                    $st=Mst_store::where('store_id',$request->store_referral_number)->first();
                    $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                    if($cnfg)
                    {
                    $joiner_points=$cnfg->joiner_points;
                    $referal_points=$cnfg->referal_points;
                    $fop=$cnfg->first_order_points;
                    $store_id=$st->store_id;
    
                    }
                    else
                    {
                        // $data['status'] = 0;
                        // $data['message'] = "No configure points added to the store";
                        // return response($data);
    
                    }
    
                }
                    // if($str)
                    // {
                    //     //dd(1);
                    //     $cnfg=Trn_configure_points::where('store_id',$str->store_id)->first();
                    //     $joiner_points=$cnfg->joiner_points;
                    //     $referal_points=$cnfg->referal_points;
                    //     $fop=$cnfg->first_order_points;
                    //     $store_id=$str->store_id;
    
                        
    
                    // }
                    // else
                    // {
                    //     $st=Mst_store::where('store_id',$request->store_referral_number)->first();
                    //     if($st)
                    //     {
                    //        // dd(2);
                    //     $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                    //     $joiner_points=$cnfg->joiner_points;
                    //     $referal_points=$cnfg->referal_points;
                    //     $fop=$cnfg->first_order_points;
                    //     $store_id=$st->store_id;
    
                    //     }
                    //     //dd(3);
    
                    // }
                    $store_referral=new Trn_store_referrals();
                    $store_referral->store_referral_number=$request->store_referral_number;
                    $store_referral->store_id=$store_id;
                    $store_referral->refered_by_id=$request->referred_customer_id??0;
                    $store_referral->refered_by_number=$request->referral_id;
                    $store_referral->joined_by_id=$customer_id;
                    $store_referral->joined_by_number=$stringRefer;
                    $store_referral->reference_status=0;
                    $store_referral->joiner_points=$joiner_points;
                    $store_referral->referral_points=$referal_points;
                    $store_referral->fop=$fop;
                    $store_referral->save();
                    // $data['status'] = 1;
                    // $data['message'] = "Success..Store Referral initiated";
                    // return response($data);
    
                }
                else
                {
                    // $data['status'] = 0;
                    // $data['message'] = "Failed...Referral was done previously";
                    // return response($data);
    
                }
            }
            else
            {
                if($request->refer_type=='APP')
                {
                $check_reference_exists_app=Trn_store_referrals::where('joined_by_number',$stringRefer)->where('refered_by_number',$request->referred_customer_ref_number)->first();
                if($check_reference_exists_app==NULL)  
                {
                    $cnfg_app=Trn_configure_points::find(1);
                    if($cnfg_app)
                    {
                    $joiner_points=$cnfg_app->joiner_points;
                    $referal_points=$cnfg_app->referal_points;
                    $fop=$cnfg_app->first_order_points;
                    $store_referral=new Trn_store_referrals();
                    $store_referral->store_referral_number=null;
                    $store_referral->store_id=null;
                    $store_referral->refered_by_id=$request->referred_customer_id??0;
                    $store_referral->refered_by_number=$request->referral_id;
                    $store_referral->joined_by_id=$customer_id;
                    $store_referral->joined_by_number=$stringRefer;
                    $store_referral->reference_status=0;
                    $store_referral->joiner_points=$joiner_points;
                    $store_referral->referral_points=$referal_points;
                    $store_referral->fop=$fop;
                    $store_referral->save();
                    // $data['status'] = 1;
                    // $data['message'] = "Success..App Referral initiated";
                    // return response($data);
                
    
                    }
                    else
                    {
                        // $data['status'] = 0;
                        // $data['message'] = "No configure points added to the store";
                        // return response($data);
    
                    }
                    
    
                }
            }
            }
                   
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Customer not found ";
                    return response($data);
                }
            }



                //**************************************************************** */


                // customer reward
                
                 if (isset($request->device_token) && isset($request->device_type)) {
                        Trn_CustomerDeviceToken::where('customer_id', $customer_id)->delete();

                        $cdt = new Trn_CustomerDeviceToken;
                        $cdt->customer_id =$customer_id;
                        $cdt->customer_device_token = $request->device_token;
                        $cdt->customer_device_type = $request->device_type;
                        $cdt->save();
                    }
                                    

                        $rewards = Mst_RewardToCustomer::where('customer_mobile_number',  $request->customer_mobile_number)->get();
                        if(count($rewards) > 0){
                            foreach ($rewards as $r) {
                                $cr1 = new Trn_customer_reward;
                                $cr1->transaction_type_id = 0;
                                $cr1->reward_points_earned = $r->reward_points;
                                $cr1->customer_id = $customer_id;
                                $cr1->order_id = null;
                                $cr1->reward_approved_date = $r->added_date;
                                $cr1->reward_point_expire_date = $r->added_date;
                                $cr1->reward_point_status = 1;
                                $cr1->discription = $r->reward_discription;
                                $cr1->save();
                            }
                          Mst_RewardToCustomer::where('customer_mobile_number', $request->customer_mobile_number)->delete();
                            
                        }
                        
                $configPoint = Trn_configure_points::find(1);

                $cr = new Trn_customer_reward;
                $cr->transaction_type_id = 0;
                $cr->reward_points_earned = $configPoint->registraion_points??0;
                $cr->customer_id = $customer_id;
                $cr->order_id = null;
                $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
                $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                $cr->reward_point_status = 1;
                $cr->discription = 'Registration points';
                if ($cr->save()) {
                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $customer_id)->get();

                    foreach ($customerDevice as $cd) {
                        $title = 'Registration points credited';
                        $body = $configPoint->registraion_points . ' points credited to your wallet..! Verify your account and Login now';
                        $clickAction = "MyWalletFragment";
                        $type = "wallet";
                        //   $body = 'Registration points credited successully..';
                        $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                    }
                }
            
                

                $data['customer_id'] = $customer_id;
                $data['otp'] = $customer_otp;
                $data['status'] = 1;
                $data['message'] = "Customer Registration Success";
                $res=Helper::sendOtp($request->customer_mobile_number,$customer_otp,1);
                $data['otp_session_id']=$res['session_id'];
            } else {
                $data['errors'] = $validator->errors();
                $data['status'] = 2;
                $data['message'] = "Customer exist! Please login";
            }

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }



    public function verifyOtp(Request $request)
    {
        $data = array();
        try {
            $otp = $request->customer_otp;

            $customer_id = $request->customer_id;

            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                //$otp = $request->otp_status;
                $session_id=$request->otp_session_id;

                $res=Helper::verifyOtp($session_id,$otp,1);
                if($res['status']=="success")
               {

                    $customer_id = $request->customer_id;
                    $customer = Trn_store_customer::Find($customer_id);
                    $customer->customer_otp_verify_status = 1;

                    if ($customer->update()) {
                        $data['status'] = 1;
                        $data['message'] = "success";
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                    }
                }
                else {
                    $data['status'] = 3;
                    $data['message'] = "OTP Mismatched"; 
                    return response($data);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "customer not found";
            }

            //  $otp_verify =  Trn_store_customer_otp_verify::where('customer_id', '=', $customer_id)->latest()->first();

            //   if($otp_verify)
            //          {
            //           $customer_otp_expirytime = $otp_verify->customer_otp_expirytime;
            //           $current_time = Carbon::now()->toDateTimeString();
            //           $customer_otp =  $otp_verify->customer_otp;

            //           if($customer_otp == $request->customer_otp)
            //               {
            //                   if($current_time < $customer_otp_expirytime)
            //                   {
            //                          $customer = Trn_store_customer::Find($customer_id);
            //                       $customer->customer_profile_status = 1;
            //                       $customer->customer_otp_verify_status = 1;
            //                       $customer->update();

            //                         $data['status'] = 1;
            //                       $data['message'] = "OTP Verifiction Success";

            //                   } else{
            //                       $data['status'] = 2;
            //                       $data['message'] = "OTP expired.click on resend OTP";	
            //                   }

            //                   }else{
            //                       $data['status'] = 3;
            //                       $data['message'] = "Incorrect OTP entered. Please enter a valid OTP.";
            //                   }
            //                   }else{
            //                       $data['status'] = 3;
            //                       $data['message'] = "OTP not found. Please click on resend OTP.";
            //                   }


            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => 'Invalid OTP...Try Again'];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => 'Invalid OTP...Try Again'];
            return response($response);
        }
    }



    public function resendOtp(Request $request, Trn_store_customer_otp_verify $otp_verify)
    {
        $data = array();
        try {
            $customer_id = $request->customer_id;
            if ($customer_id) {
                $customer=Trn_store_customer::findorFail($customer_id);
                $otp_verify = Trn_store_customer_otp_verify::where('customer_id', '=', $customer_id)->latest()->first();
                if ($otp_verify !== null) {
                    $customer_otp_verify_id = $otp_verify->customer_otp_verify_id;
                    $customer_otp = rand(100000,999999);
                    $customer_otp_verify_id = $otp_verify->customer_otp_verify_id;
                    $otp_verify = Trn_store_customer_otp_verify::Find($customer_otp_verify_id);
                    $extented_time = Carbon::now()->addMinute(10);
                    $otp_verify->customer_otp_expirytime = $extented_time;
                    $otp_verify->customer_otp=$customer_otp;
                    $otp_verify->update();
                    $res=Helper::sendOtp($customer->customer_mobile_number,$customer_otp,1);
                    $data['otp_session_id']=$res['session_id'];
                    $data['status'] = 1;
                    $data['otp'] = $customer_otp;
                    $data['message'] = "OTP resent Success.";
                } else {
                    $otp_verify = new Trn_store_customer_otp_verify;
                    $customer_otp = rand(100000,999999);
                    $customer_otp_expirytime = Carbon::now()->addMinute(10);
                    $otp_verify->customer_id                 = $customer_id;
                    $otp_verify->customer_otp_expirytime     = $customer_otp_expirytime;
                    $otp_verify->customer_otp                 = $customer_otp;
                    $otp_verify->save();
                    $res=Helper::sendOtp($customer->customer_mobile_number,$customer_otp,1);
                    $data['otp_session_id']=$res['session_id'];
                    $data['status'] = 2;
                    $data['otp'] = $customer_otp;
                    $data['message'] = "OTP registerd successfully. Please verify OTP.";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer Doesn't Exist.";
            }


            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function FpverifyMobile(Request $request, Trn_store_customer_otp_verify $otp_verify)
    {
        $data = array();
        try {
            // 	$mobCode = $request->country_code;

            $customer_mobile_number = $request->customer_mobile_number;
            $mobCheck = Trn_store_customer::where("customer_mobile_number", '=', $customer_mobile_number)->latest()->first();

            if ($mobCheck) {

                $customer_id = $mobCheck->customer_id;
                $customer_mobile_number = $mobCheck->customer_mobile_number;

                $validator = Validator::make(
                    $request->all(),
                    [
                        // 'country_code' => 'required',
                        'customer_mobile_number' => 'required'
                    ],
                    [
                        // 'country_code.required' => "Country Code is required",
                        'customer_mobile_number.required' => "Mobile number is required",


                    ]
                );

                if (!$validator->fails()) {
                    $customer_otp =  rand(100000,999999);
                    $customer_otp_expirytime = Carbon::now()->addMinute(10);

                    $otp_verify->customer_id                 = $customer_id;
                    $otp_verify->customer_otp_expirytime     = $customer_otp_expirytime;
                    $otp_verify->customer_otp                = $customer_otp;
                    $otp_verify->save();

                    $data['status'] = 1;
                    $data['customer_id'] = $customer_id;
                    $data['customer_mobile_number'] = $customer_mobile_number;
                    $data['customer_otp'] = $customer_otp;
                    $res=Helper::sendOtp($customer_mobile_number,$customer_otp,1);
                    $data['otp_session_id']=$res['session_id'];
                    $data['message'] = "Mobile Verification Success. OTP Sent to registered mobile number";
                } else {

                    $data['status'] = 0;
                    $data['errors'] = $validator->errors();
                    $data['message'] = "Verification Failed";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer Does not exist";
            }
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }


    public function FpverifyOTP(Request $request, Trn_store_customer_otp_verify $otp_verify)
    {
        $data = array();
        try {
            $otp = $request->customer_otp;
            // $mobCode = $request->country_code;
            $mobNumber = $request->customer_mobile_number;
            $otp_session_id=$request->otp_session_id;

            $mobCheck = Trn_store_customer::where("customer_mobile_number", '=', $mobNumber)->latest()->first();
            if ($mobCheck) {
                $customer_id = $mobCheck->customer_id;
                $customer_mobile_number = $mobCheck->customer_mobile_number;
                $otpCheck = Trn_store_customer_otp_verify::where('customer_id', '=', $customer_id)->where('customer_otp', '=', $otp)->latest()->first();

                if ($otpCheck) {
                    $customer_otp_expirytime = $otpCheck->customer_otp_expirytime;
                    $current_time = Carbon::now()->toDateTimeString();
                    $customer_new_otp =  $otpCheck->customer_otp;

                    // $expParse = $expTime->format('Y-m-d H:i:s');

                    if ($current_time < $customer_otp_expirytime) {

                        $res=Helper::verifyOtp($otp_session_id,$otp,1);
                        if($res['status']=="success")
                        {
 
                         $data['status'] = 1;
                         $data['customer_id'] = $customer_id;
                         $data['customer_mobile_number'] = $customer_mobile_number;
                         $data['message'] = "OTP verification success. Enter a new password.";
                        }
                        else
                        {
                         $data['status'] = 3;
                         $data['message'] = "OTP Mismatched";  
                            
                        }
                    } else {
                        $data['status'] = 2;
                        $data['message'] = "OTP expired.click on resend OTP";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Invalid OTP Entered";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer Does not exist";
            }

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }


    public function resetPassword(Request $request)
    {
        $data = array();
        try {
            $customer_id = $request->customer_id;
            $mobNumber = $request->customer_mobile_number;
            $mobCheck = Trn_store_customer::where("customer_mobile_number", '=', $mobNumber)->where('customer_id', '=', $customer_id)->first();
            if ($mobCheck) {
                $validator = Validator::make($request->all(), [
                    'password' => 'required|string|min:6|confirmed'
                ]);
                if (!$validator->fails()) {
                    $encPass = Hash::make($request->input('password'));
                    Trn_store_customer::where('customer_id', $customer_id)->where("customer_mobile_number", '=', $mobNumber)->update(['password' => $encPass]);
                    $data['status'] = "1";
                    $data['messsage'] = "Password Changed successfully";
                } else {
                    $data['status'] = "0";
                    $data['errors'] = $validator->errors();
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer Does not exist";
            }
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function viewInfo(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && $cData = Trn_store_customer::find($request->customer_id)) {
                if (isset($request->country)) {
                    $contryId = Country::where('country_name', 'LIKE', "%{$request->country}%")->first()->country_id;
                }
                if (isset($request->state)) {
                    $stateId = State::where('state_name', 'LIKE', "%{$request->state}%")->first()->state_id;
                }

                if (!isset($cData->country_id) && !isset($cData->state_id)) {
                    Trn_store_customer::where('customer_id', $request->customer_id)->update(['country_id' => @$contryId, 'state_id' => @$stateId]);
                }

                $data['customerData'] = Trn_store_customer::find($request->customer_id);

                if (!isset($data['customerData']->customer_last_name)) {
                    $data['customerData']->customer_last_name = '';
                }

                $ConfigPoints = Trn_configure_points::first();

                $data['customerData']->joiner_points = @$ConfigPoints->joiner_points;
                $data['customerData']->referal_points = @$ConfigPoints->referal_points;
                $data['cid']=$data['customerData']->country_id;
                $cn=DB::table('sys_countries')->where('country_id', $data['customerData']->country_id)->first();
                $data['customerData']['country_name']=$cn->country_name??'';
                $st=DB::table('sys_states')->where('state_id', $data['customerData']->state_id)->first();
                $data['customerData']['state_name']=$st->state_name??'';
                $di=DB::table('mst_districts')->where('district_id',$data['customerData']->district_id)->first();
                $data['customerData']['district_name']=$di->district_name??'';
                $pin=DB::table('mst_towns')->where('town_id',$data['customerData']->town_id)->first();
                $data['customerData']['pincode']=$pin->town_name??'';

                $data['customerData']->customerAddress = Trn_customerAddress::where('customer_id', $request->customer_id)->get();
                foreach ($data['customerData']->customerAddress as $a) {
                    if (!isset($a->default_status))
                        $a->default_status = 0;

                    $a->stateData = @$a->stateFunction['state_name'];
                    $a->districtData = @$a->districtFunction['district_name'];
                }
                $data['status'] = 1;
                $data['message'] = "Success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function addAddress(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'address'          => 'required',
                    ]
                );

                if (!$validator->fails()) {

                    $addr = new Trn_customerAddress;
                    $addr->customer_id = $request->customer_id;
                    $addr->address = $request->address;
                    $addr->name = $request->name;
                    $addr->phone = $request->phone;
                    $addr->state = $request->state;
                    $addr->district = $request->district;
                    $addr->street = $request->street;
                    $addr->pincode = $request->pincode;


                    $addr->longitude = $request->longitude;
                    $addr->latitude = $request->latitude;
                    $addr->place = $request->place;

                    // $addr->default_status = $request->default_status;

                    if ($request->default_status != 1) {
                        $addr->default_status = 0;
                    } else {
                        $countAddress =  Trn_customerAddress::where('customer_id', $request->customer_id)->update(['default_status' => 0]);
                        $addr->default_status = $request->default_status;
                    }

                    if ($addr->save()) {
                        $data['status'] = 1;
                        $data['message'] = "Address added";
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Address required";
                    return response($data);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }



    public function editAddress(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                if (isset($request->customer_address_id) && Trn_customerAddress::find($request->customer_address_id)) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'address'          => 'required',
                        ]
                    );

                    if (!$validator->fails()) {

                        if ($request->default_status == 1) {
                            $countAddress =  Trn_customerAddress::where('customer_id', $request->customer_id)->update(['default_status' => 0]);
                        } else {



                            $countAddress =  Trn_customerAddress::where('customer_id', $request->customer_id)->first();
                            Trn_customerAddress::where('customer_address_id', $countAddress->customer_address_id)->update(['default_status' => 1]);
                        }


                        // $addr = Trn_customerAddress::find($request->customer_address_id);
                        $addr['address'] = $request->address;
                        $addr['name'] = $request->name;
                        $addr['phone'] = $request->phone;
                        $addr['state'] = $request->state;
                        $addr['district'] = $request->district;
                        $addr['street'] = $request->street;
                        $addr['pincode'] = $request->pincode;

                        $addr['longitude'] = $request->longitude;
                        $addr['latitude'] = $request->latitude;
                        $addr['place'] = $request->place;
                        $addr['default_status'] = $request->default_status;

                        // if($request->default_status == 'one')
                        // {
                        //      $countAddress =  Trn_customerAddress::where('customer_id',$request->customer_id)->update(['default_status' => 0]);
                        //     $addr['default_status'] = 1;

                        // }
                        // else
                        // {
                        //     $addr['default_status'] = 0;

                        // }

                        if (Trn_customerAddress::where('customer_address_id', $request->customer_address_id)->update($addr)) {
                            // $countAddress =  Trn_customerAddress::where('customer_id',$request->customer_id)
                            // ->where('customer_address_id','!=',$request->customer_address_id)->update(['default_status' => 0]);


                            if ($request->default_status != 1) {
                                $countAddress =  Trn_customerAddress::where('customer_id', $request->customer_id)->first();
                                Trn_customerAddress::where('customer_address_id', $countAddress->customer_address_id)->update(['default_status' => 1]);
                            }


                            $data['status'] = 1;
                            $data['message'] = "Address updated";
                            $data['data'] = $request->all();
                            return response($data);
                        } else {
                            $data['status'] = 0;
                            $data['message'] = "failed";
                            return response($data);
                        }
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "Address required";
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Address not found ";
                    return response($data);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function removeAddress(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_address_id) && Trn_customerAddress::find($request->customer_address_id)) {
                $addr = Trn_customerAddress::find($request->customer_address_id);
                if ($addr->delete()) {
                    if ($secAddress =  Trn_customerAddress::where('customer_id', $addr->customer_id)->first()) {
                        $secAddress->default_status = 1;
                        $secAddress->update();
                    }

                    $data['status'] = 1;
                    $data['message'] = "Address removed";
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                    return response($data);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Address not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }


    public function viewAddress(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_address_id) && Trn_customerAddress::find($request->customer_address_id)) {
                $data['addressData'] = Trn_customerAddress::find($request->customer_address_id);
                $data['addressData']->stateData = $data['addressData']->stateFunction['state_name'];
                $data['addressData']->districtData = $data['addressData']->districtFunction['district_name'];
                $data['status'] = 1;
                $data['message'] = "success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "Address not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }


    public function updateProfile(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'customer_name'          => 'required',
                        // 'customer_email'          => 'required|email',
                        'customer_mobile_number'          => 'required|unique:trn_store_customers,customer_mobile_number,' . $request->customer_id . ',customer_id',
                        //'address'          => 'required',
                    ]
                );

                if (!$validator->fails()) {
                    //if(Trn_store_customer::where('customer_mobile_number',$request->customer_mobile_number)->first())
                    // {

                    // }

                    $cusName = explode(' ', $request->customer_name, 2);

                    $customer = Trn_store_customer::find($request->customer_id);

                    $customer->customer_first_name            = $cusName[0];
                    $customer->customer_last_name            = @$cusName[1];
                    $customer->customer_email   = $request->customer_email;
                    $customer->gender   = $request->gender;
                    $customer->dob   = $request->dob;
                    $customer->customer_mobile_number   = $request->customer_mobile_number;

                    $customer->latitude   = $request->latitude;
                    $customer->longitude   = $request->longitude;
                    $customer->place   = $request->place;


                    $customer->country_id = $request->country_id;
                    $customer->state_id   = $request->state_id;
                    $customer->district_id   = $request->district_id;
                    $customer->town_id   = $request->town_id;
                    $customer->customer_pincode   = $request->pincode;

                    if ($customer->update()) {
                        $data['status'] = 1;
                        $data['message'] = "Profile updated";
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['errors'] = $validator->errors();
                    $data['message'] = "failed";
                    return response($data);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }


    public function updatePassword(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'old_password'          => 'required',
                        'password' => 'required|min:6|confirmed',

                    ],
                    [
                        'old_password.required'        => 'Old password required',
                        'password.required'        => 'Password required',
                        'password.confirmed'        => 'Passwords not matching',
                    ]
                );

                if (!$validator->fails()) {

                    $customer = Trn_store_customer::find($request->customer_id);

                    if (Hash::check($request->old_password, $customer->password)) {
                        $data20 = [
                            'password'      => Hash::make($request->password),
                        ];
                        Trn_store_customer::where('customer_id', $request->customer_id)->update($data20);

                        $data['status'] = 1;
                        $data['message'] = "Password updated successfully.";
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "Old password incorrect.";
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['errors'] = $validator->errors();
                    $data['message'] = "failed";
                    return response($data);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }


    public function totalRewardList(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $data['customerRewards'] = Trn_customer_reward::where('customer_id', $request->customer_id)->where('reward_point_status', 1)->get();
                foreach ($data['customerRewards'] as $cr) {
                    $cr->rewardTransactionType = Trn_customer_reward_transaction_type::find(@$cr->transaction_type_id);
                }

                $data['status'] = 1;
                $data['message'] = "Success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function totalRewardCount(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $customerRewardsCount = Trn_customer_reward::where('customer_id', $request->customer_id)->where('reward_point_status', 1)->sum('reward_points_earned');
                $data['customerRewardsCount'] = $customerRewardsCount - Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                // foreach($data['customerRewards'] as $cr)
                // {
                //     $cr->rewardTransactionType = Trn_customer_reward_transaction_type::find(@$cr->transaction_type_id);
                // }

                $data['status'] = 1;
                $data['message'] = "Success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
    public function initiateReferral(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                if($request->joined_customer_ref_number==$request->referred_customer_ref_number)
                {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Reference.Cannot initiate a reference created by yourselves";
                    return response($data);

                }
          
            if($request->store_referral_number)
            {
            $check_reference_exists=Trn_store_referrals::where('joined_by_number',$request->joined_customer_ref_number)->where('refered_by_number',$request->referred_customer_ref_number)->where('store_referral_number',$request->store_referral_number)->first();
            if($check_reference_exists==NULL)  
            {
                //$request->store_referral_number
                $joiner_points=0;
                $referal_points=0;
                $fop=0;
                $store_id=0;
                
                $str=Mst_store::where('store_referral_id',$request->store_referral_number)->first();
                if($str)
                {
                if(is_null($str->store_referral_id))
                {
                    $st_uid=$str->store_id;
                    $cnfg=Trn_configure_points::where('store_id',$st_uid)->first();
                    if($cnfg)
                    {
                        $joiner_points=$cnfg->joiner_points;
                        $referal_points=$cnfg->referal_points;
                        $fop=$cnfg->first_order_points;
                        $store_id=$str->store_id;

                    }
                    else
                    {
                        $data['status'] = 0;
                        $data['store_id']=$st_uid;
                        $data['message'] = "No configure points added to the store";
                        return response($data);

                    }
                   

                }
                else
                {
                    $st_uid=$str->store_referral_id;
                    $st=Mst_store::where('store_referral_id',$st_uid)->first();
                    if($st)
                    {
                       // dd(2);
                    $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                    if($cnfg)
                    {
                    $joiner_points=$cnfg->joiner_points;
                    $referal_points=$cnfg->referal_points;
                    $fop=$cnfg->first_order_points;
                    $store_id=$st->store_id;

                    }
                    else
                    {
                        $store_id=$st->store_id;
                        $data['status'] = 0;
                        $data['store_id']=$store_id;
                        $data['message'] = "No configure points added to the store";
                        return response($data);

                    }
                    

                    }

                }
            }
            else
            {

                $st=Mst_store::where('store_id',$request->store_referral_number)->first();
                $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                if($cnfg)
                {
                $joiner_points=$cnfg->joiner_points;
                $referal_points=$cnfg->referal_points;
                $fop=$cnfg->first_order_points;
                $store_id=$st->store_id;

                }
                else
                {
                    $store_id=$st->store_id;
                    $data['status'] = 0;
                    $data['store_id']=$store_id;
                    $data['message'] = "No configure points added to the store";
                    return response($data);

                }

            }
                // if($str)
                // {
                //     //dd(1);
                //     $cnfg=Trn_configure_points::where('store_id',$str->store_id)->first();
                //     $joiner_points=$cnfg->joiner_points;
                //     $referal_points=$cnfg->referal_points;
                //     $fop=$cnfg->first_order_points;
                //     $store_id=$str->store_id;

                    

                // }
                // else
                // {
                //     $st=Mst_store::where('store_id',$request->store_referral_number)->first();
                //     if($st)
                //     {
                //        // dd(2);
                //     $cnfg=Trn_configure_points::where('store_id',$st->store_id)->first();
                //     $joiner_points=$cnfg->joiner_points;
                //     $referal_points=$cnfg->referal_points;
                //     $fop=$cnfg->first_order_points;
                //     $store_id=$st->store_id;

                //     }
                //     //dd(3);

                // }
                $store_referral=new Trn_store_referrals();
                $store_referral->store_referral_number=$request->store_referral_number;
                $store_referral->store_id=$store_id;
                $store_referral->refered_by_id=$request->referred_customer_id??0;
                $store_referral->refered_by_number=$request->referred_customer_ref_number;
                $store_referral->joined_by_id=$request->customer_id;
                $store_referral->joined_by_number=$request->joined_customer_ref_number;
                $store_referral->reference_status=0;
                $store_referral->joiner_points=$joiner_points;
                $store_referral->referral_points=$referal_points;
                $store_referral->fop=$fop;
                $store_referral->save();
                $data['status'] = 1;
                $data['store_id']=$store_id;
                $data['message'] = "Success..Store Referral initiated";
                return response($data);

            }
            else
            {
                $sr=Mst_store::where('store_referral_id',$request->store_referral_number)->first();
                $s_id=$sr->store_id;
                $data['status'] = 0;
                $data['store_id']=$s_id;
                $data['message'] = "Failed...Referral was done previously";
                return response($data);

            }
        }
        else
        {
            
            $check_reference_exists_app=Trn_store_referrals::where('joined_by_number',$request->joined_customer_ref_number)->where('refered_by_number',$request->referred_customer_ref_number)->first();
            if($check_reference_exists_app==NULL)  
            {
                $cnfg_app=Trn_configure_points::find(1);
                if($cnfg_app)
                {
                $joiner_points=$cnfg_app->joiner_points;
                $referal_points=$cnfg_app->referal_points;
                $fop=$cnfg_app->first_order_points;
                $store_referral=new Trn_store_referrals();
                $store_referral->store_referral_number=null;
                $store_referral->store_id=null;
                $store_referral->refered_by_id=$request->referred_customer_id??0;
                $store_referral->refered_by_number=$request->referred_customer_ref_number;
                $store_referral->joined_by_id=$request->customer_id;
                $store_referral->joined_by_number=$request->joined_customer_ref_number;
                $store_referral->reference_status=0;
                $store_referral->joiner_points=$joiner_points;
                $store_referral->referral_points=$referal_points;
                $store_referral->fop=$fop;
                $store_referral->save();
                $data['status'] = 1;
                $data['message'] = "Success..App Referral initiated";
                return response($data);
            

                }
                else
                {
                    $data['status'] = 0;
                    $data['message'] = "No configure points added to the store";
                    return response($data);

                }
                

            }
        
        }
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
}
