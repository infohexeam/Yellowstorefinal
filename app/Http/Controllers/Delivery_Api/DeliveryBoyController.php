<?php

namespace App\Http\Controllers\Delivery_Api;

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

use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Trn_StoreDeliveryBoyOtpVerify;
use App\Models\admin\Trn_DeliveryBoyLocation;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_DeliveryBoyDeviceToken;

use App\Models\admin\State;
use App\Models\admin\Country;
use App\Models\admin\Trn_delivery_boy_otp_verify;

class DeliveryBoyController extends Controller
{

    public function dBoy(Request $request)
    {
        $data = array();
        try {
            $data['delivery'] = Mst_delivery_boy::all();
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function logout(Request $request)
    {
        //dd($request->delivery_boy_id);
        if (isset($request->delivery_boy_id) && Mst_delivery_boy::find($request->delivery_boy_id)) {

            $accessToken = auth()->user()->token();
         //   dd($accessToken);
            auth()->guard('api-delivery')->user()->delivery_boy_id;
            $token = $request->user()->tokens->find($accessToken);
            $token->revoke();
            Trn_DeliveryBoyDeviceToken::where('delivery_boy_id',$request->delivery_boy_id)->delete();

            $data['status'] = 1;
            $data['message'] = "Success";
        } else {
            $data['status'] = 0;
            $data['message'] = "Failed";
        }
        return response($data);

        // $accessToken = auth()->guard('delivery')->user()->token();
        // $token = $request->user()->tokens->find($accessToken);
        // $token->revoke();
        // $data['status'] = 1;
        // $data['message'] = "Success";
        // return response($data);
    }

    public function loginDelivery(Request $request)
    {
        $data = array();
        try {
            $phone = $request->input('delivery_boy_mobile');
            $passChk = $request->input('password');
            // $devType = $request->input('device_type');
            // $devToken = $request->input('device_token');

            $validator = Validator::make(
                $request->all(),
                [
                    'delivery_boy_mobile' => 'required',
                    'password' => 'required',
                    // 'device_type' => 'required',
                    // 'device_token' => 'required',
                ],
                [
                    'delivery_boy_mobile.required' => "Mobile Number is required",
                    'password.required' => "Password is required",
                    // 'device_type.required' => "Device Type is required",
                    // 'device_toke.required' => "Device Token is required",
                ]
            );
            // dd($validator);
            if (!$validator->fails()) {
                $custCheck = Mst_delivery_boy::where('delivery_boy_mobile', '=', $phone)->first();
                $today = Carbon::now()->toDateString();

                if ($custCheck) {
                    
                    if (Hash::check($passChk, $custCheck->password)) {
                       if($custCheck->delivery_boy_otp_verify_status != 1)
                       {
                            $data['status']=2;
                            $db_otp =  rand (100000,999999);
                            $db_otp_expirytime = Carbon::now()->addMinute(10);
                            $otp_verify=new Trn_StoreDeliveryBoyOtpVerify();
                            $otp_verify->delivery_boy_id   = $custCheck->delivery_boy_id;
                            $otp_verify->otp_expirytime     = $db_otp_expirytime;
                            $otp_verify->otp                 = $db_otp;
                            $otp_verify->save();
                            $data['delivery_boy_id']=$custCheck->delivery_boy_id;
                            $data['message']='Please verify otp to login!';
                            $res=Helper::sendOtp($phone,$db_otp,2);
                            $data['otp_session_id']=$res['session_id'];
                            return response($data);
                       }
                        if ($custCheck->delivery_boy_status != 0) {
                            if (Auth::guard('delivery')->attempt(['delivery_boy_mobile' => request('delivery_boy_mobile'), 'password' => request('password')])) {
                                $user = Mst_delivery_boy::find(auth()->guard('delivery')->user()->delivery_boy_id);
                                if (isset($request->device_token)) {

                                    Trn_DeliveryBoyDeviceToken::where('delivery_boy_id', auth()->guard('delivery')->user()->delivery_boy_id)->delete();
                                    $ddt =  new Trn_DeliveryBoyDeviceToken;
                                    $ddt->delivery_boy_id = auth()->guard('delivery')->user()->delivery_boy_id;
                                    $ddt->dboy_device_token = $request->device_token;
                                    $ddt->dboy_device_type = $request->device_token;
                                    $ddt->save();
                                }
                                //dd($user);
                                $data['token'] =  $user->createToken('authToken', ['delivery'])->accessToken;
                                $data['status'] = 1;
                                $data['message'] = "Login Success";

                                $data['delivery_boy_id'] = $user->delivery_boy_id;
                                $data['delivery_boy_name'] = $user->delivery_boy_name;
                                $data['delivery_boy_mobile'] = $user->delivery_boy_mobile;
                                $data['availability_status'] = $user->availability_status;
                            }
                        } else {
                            $data['status'] = 4;
                            $data['message'] = "Profile not activated";
                        }
                    } else {
                        $data['status'] = 3;
                        $data['message'] = "Mobile Number or Password is Invalid";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Mobile Number";
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
    public function saveDeliveryBoy(Request $request)
    {
        $data = array();
        try {
            $validator = Helper::validateDeliveryBoy($request->all());
            if (!$validator->fails())
             {
                $delivery_boy=new Mst_delivery_boy();
                $delivery_boy->delivery_boy_name = $request->delivery_boy_name;
                $delivery_boy->delivery_boy_mobile = $request->delivery_boy_mobile;
                $delivery_boy->delivery_boy_email = $request->delivery_boy_email;
                $delivery_boy->delivery_boy_address = $request->delivery_boy_address;
                $delivery_boy->vehicle_number = $request->vehicle_number;
                $delivery_boy->vehicle_type_id = $request->vehicle_type_id;
                //$delivery_boy->store_id = $store_id;
                $delivery_boy->country_id = $request->country_id;
                $delivery_boy->state_id = $request->state_id;
                $delivery_boy->district_id = $request->district_id;
                $delivery_boy->town_id = $request->town_id;
                $delivery_boy->is_registered=1;
                $delivery_boy->latitude=$request->latitude;
                $delivery_boy->longitude=$request->longitude;
                $delivery_boy->delivery_boy_username = $request->delivery_boy_username;
                $delivery_boy->password  = Hash::make($request->delivery_boy_password);
                $delivery_boy->delivery_boy_status = 0;
                $delivery_boy->delivery_boy_otp_verify_status = 0;
                $delivery_boy->save();
                $db_id = DB::getPdo()->lastInsertId();
                $data['status']=1;
                $db_otp =  rand (100000,999999);
                $db_otp_expirytime = Carbon::now()->addMinute(10);
                $otp_verify=new Trn_StoreDeliveryBoyOtpVerify();
                $otp_verify->delivery_boy_id                 = $db_id;
                $otp_verify->otp_expirytime     = $db_otp_expirytime;
                $otp_verify->otp                 = $db_otp;
                $otp_verify->save();
                $data['delivery_boy_id']=$db_id;
                $data['message']='Delivery boy registration successful';
                $res=Helper::sendOtp($delivery_boy->delivery_boy_mobile,$db_otp,2);
                $data['otp_session_id']=$res['session_id'];
                return response($data);
            }
            else
            {
                $data['status']=0;
                $data['message'] ='Validation failed.';
                $data['errors'] = $validator->errors();
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
    public function verifyOtp(Request $request)
    {
        $data = array();
        try {
            $otp = $request->delivery_boy_otp;

            $delivery_boy_id = $request->delivery_boy_id;

            if (isset($request->delivery_boy_id) && Mst_delivery_boy::find($request->delivery_boy_id)) {
                //$otp = $request->otp_status;
                $session_id=$request->otp_session_id;

                $res=Helper::verifyOtp($session_id,$otp,1);
                if($res['status']=="success")
               {

                    $delivery_boy_id = $request->delivery_boy_id;
                    $delivery_boy = Mst_delivery_boy::Find($delivery_boy_id);
                    $delivery_boy->delivery_boy_otp_verify_status = 1;

                    if ($delivery_boy->update()) {
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



    public function viewProfile(Request $request)
    {
        $data = array();

        try {
            if (isset($request->delivery_boy_id) && $dboyData = Mst_delivery_boy::find($request->delivery_boy_id)) {

                if (isset($request->country)) {
                    $contryId = Country::where('country_name', 'LIKE', "%{$request->country}%")->first()->country_id;
                }
                if (isset($request->state)) {
                    $stateId = State::where('state_name', 'LIKE', "%{$request->state}%")->first()->state_id;
                }
                if (!isset($dboyData->country_id) && !isset($dboyData->state_id)) {
                    Mst_delivery_boy::where('delivery_boy_id', $request->delivery_boy_id) ->update(['country_id' => @$contryId, 'state_id' => @$stateId]);
                }


                $deliveryBoyData = Mst_delivery_boy::find($request->delivery_boy_id);
                $cn=DB::table('sys_countries')->where('country_id',$deliveryBoyData->country_id)->first();
                $deliveryBoyData->country_name=$cn->country_name;
                $st=DB::table('sys_states')->where('state_id',$deliveryBoyData->state_id)->first();
                $deliveryBoyData->state_name=$st->state_name;
                $di=DB::table('mst_districts')->where('district_id',$deliveryBoyData->district_id)->first();
                $deliveryBoyData->district_name=$di->district_name;
                $pin=DB::table('mst_towns')->where('town_id',$deliveryBoyData->town_id)->first();
                $deliveryBoyData->pincode=$pin->town_name;
                $data['deliveryBoyData'] = $deliveryBoyData;
                $data['status'] = 1;
                $data['message'] = "success";
            } else {
                $data['status'] = 0;
                $data['message'] = "Delivery boy not found ";
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

    public function updateProfile(Request $request)
    {
        $data = array();

        try {

            if (isset($request->delivery_boy_id) && Mst_delivery_boy::find($request->delivery_boy_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'delivery_boy_name' => 'required',
                        //'delivery_boy_email' => 'required',
                        'availability_status' => 'required',
                    ],
                    [
                        'delivery_boy_name.required' => "Name is required",
                        'delivery_boy_email.required' => "Email is required",
                        'availability_status.required' => "Availability status required",
                    ]
                );

                if (!$validator->fails()) {
                    $dboy = Mst_delivery_boy::find($request->delivery_boy_id);
                    $dboy->delivery_boy_name = $request->delivery_boy_name;
                    $dboy->delivery_boy_email = $request->delivery_boy_email;
                    $dboy->availability_status = $request->availability_status;

                    $dboy->country_id = $request->country_id;
                    $dboy->state_id   = $request->state_id;
                    $dboy->district_id   = $request->district_id;
                    $dboy->town_id   = $request->town_id;

                    $dboy->update();

                    $data['message'] = "success";
                    $data['status'] = 1;
                } else {
                    $data['errors'] = $validator->errors();
                    $data['message'] = "failed";
                    $data['status'] = 0;
                }
            } else {
                $data['message'] = "Delivery boys not found";
                $data['status'] = 0;
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

    public function updatePassword(Request $request)
    {
        $data = array();

        try {
            if (isset($request->delivery_boy_id) && Mst_delivery_boy::find($request->delivery_boy_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'old_password'          => 'required',
                        'password' => 'required|confirmed',

                    ],
                    [
                        'old_password.required'        => 'Old password required',
                        'password.required'        => 'Password required',
                        'password.confirmed'        => 'Passwords not matching',
                    ]
                );

                if (!$validator->fails()) {

                    $customer = Mst_delivery_boy::find($request->delivery_boy_id);

                    if (Hash::check($request->old_password, $customer->password)) {
                        $data20 = [
                            'password'      => Hash::make($request->password),
                        ];
                        Mst_delivery_boy::where('delivery_boy_id', $request->delivery_boy_id)->update($data20);

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
                $data['message'] = "Delivery boy not found ";
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


    public function FpverifyMobile(Request $request, Trn_StoreDeliveryBoyOtpVerify $otp_verify)
    {
        $data = array();
        try {

            $delivery_boy_mobile = $request->delivery_boy_mobile;
            $mobCheck = Mst_delivery_boy::where("delivery_boy_mobile", '=', $delivery_boy_mobile)->latest()->first();

            if ($mobCheck) {
                $delivery_boy_id = $mobCheck->delivery_boy_id;
                $delivery_boy_mobile = $mobCheck->delivery_boy_mobile;

                $validator = Validator::make(
                    $request->all(),
                    [
                        'delivery_boy_mobile' => 'required'
                    ],
                    [
                        'delivery_boy_mobile.required' => "Delivery boy mobile number is required",
                    ]
                );

                if (!$validator->fails()) {
                    $otp =  rand(100000, 999999);
                    $otp_expirytime = Carbon::now()->addMinute(10);

                    $otp_verify->delivery_boy_id                 = $delivery_boy_id;
                    $otp_verify->otp_expirytime     = $otp_expirytime;
                    $otp_verify->otp                = $otp;
                    $otp_verify->save();
                    $res=Helper::sendOtp($delivery_boy_mobile,$otp,1);
                    //return $dboy->delivery_boy_mobile;
                    $data['otp_session_id']=$res['session_id'];
                    $data['status'] = 1;
                    $data['delivery_boy_id'] = $delivery_boy_id;
                    $data['delivery_boy_mobile'] = $delivery_boy_mobile;
                    $data['otp'] = $otp;
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


    public function FpverifyOTP(Request $request, Trn_StoreDeliveryBoyOtpVerify $otp_verify)
    {
        $data = array();
        try {
            $otp = $request->otp;
            $mobNumber = $request->delivery_boy_mobile;
            $otp_session_id=$request->otp_session_id;

            $mobCheck = Mst_delivery_boy::where("delivery_boy_mobile", '=', $mobNumber)->latest()->first();
            if ($mobCheck) {
                $delivery_boy_id = $mobCheck->delivery_boy_id;
                $mobile_number = $mobCheck->delivery_boy_mobile;
                $otpCheck = Trn_StoreDeliveryBoyOtpVerify::where('delivery_boy_id', '=', $delivery_boy_id)->where('otp', '=', $otp)->latest()->first();

                if ($otpCheck) {
                    $otp_expirytime = $otpCheck->otp_expirytime;
                    $current_time = Carbon::now()->toDateTimeString();
                    $new_otp =  $otpCheck->otp;

                    // $expParse = $expTime->format('Y-m-d H:i:s');

                    if ($current_time < $otp_expirytime) {
                        $res=Helper::verifyOtp($otp_session_id,$new_otp,1);
                        if($res['status']=="success")
                        {
                            $data['status'] = 1;
                            $data['delivery_boy_id'] = $delivery_boy_id;
                            $data['delivery_boy_mobile'] = $mobile_number;
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
                $data['message'] = "Delivery boy does not exist";
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
            // $delivery_boy_id = $request->delivery_boy_id; // inorder to solve the space issue in apk side
            $mobNumber = $request->delivery_boy_mobile;
            $mobCheck = Mst_delivery_boy::where('delivery_boy_mobile', '=', $mobNumber)->first();
            if ($mobCheck) {
                $delivery_boy_id = $mobCheck->delivery_boy_id;
                $validator = Validator::make($request->all(), [
                    'password' => 'required|string|min:8|confirmed'
                ]);
                if (!$validator->fails()) {
                    $encPass = Hash::make($request->input('password'));
                    Mst_delivery_boy::where('delivery_boy_id', $delivery_boy_id)->where("delivery_boy_mobile", '=', $mobNumber)->update(['password' => $encPass]);
                    $data['status'] = "1";
                    $data['messsage'] = "Password Changed successfully";
                } else {
                    $data['status'] = "0";
                    $data['errors'] = $validator->errors();
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Delivery boy does not exist";
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

    public function resendOtp(Request $request, Mst_delivery_boy $dboy, Trn_StoreDeliveryBoyOtpVerify $otp_verify)
    {
        $data = array();
        try {
            $delivery_boy_id = $request->delivery_boy_id;
            $delivery_boy=Mst_delivery_boy::find($delivery_boy_id);
            $otp_verify = Trn_StoreDeliveryBoyOtpVerify::where('delivery_boy_id', '=', $delivery_boy_id)->latest()->first();
            if ($otp_verify) {

                if ($otp_verify !== null) {
                    $store_delivery_boy_otp_verify_id = $otp_verify->store_delivery_boy_otp_verify_id;
                    $otp_verify = Trn_StoreDeliveryBoyOtpVerify::Find($store_delivery_boy_otp_verify_id);
                    $extented_time = Carbon::now()->addMinute(10);
                    $otp_verify->otp_expirytime = $extented_time;
                    $otp_verify->update();
                    //return $request->delivery_boy_id;
                    $res=Helper::sendOtp($delivery_boy->delivery_boy_mobile,$otp_verify->otp,1);
                    //return $dboy->delivery_boy_mobile;
                    $data['otp_session_id']=$res['session_id'];
                    $data['status'] = 1;
                    $data['otp'] = $otp_verify->otp;
                    $data['message'] = "OTP resent Success.";
                } else {
                    $otp_verify = new Trn_StoreDeliveryBoyOtpVerify;
                    $otp =  rand(100000, 999999);
                    $otp_expirytime = Carbon::now()->addMinute(10);
                    $otp_verify->delivery_boy_id          = $delivery_boy_id;
                    $otp_verify->otp_expirytime     = $otp_expirytime;
                    $otp_verify->otp                 = $otp;
                    $otp_verify->save();
                    $res=Helper::sendOtp($delivery_boy->delivery_boy_mobile,$otp,1);
                    $data['otp_session_id']=$res['session_id'];
                    $data['status'] = 2;
                    $data['message'] = "OTP registerd successfully. Please verify OTP.";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Delivery boy doesn't Exist.";
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


    public function updateLoc(Request $request)
    {
        $data = array();
        try {
            if (isset($request->delivery_boy_id) && Mst_delivery_boy::find($request->delivery_boy_id)) {
                $delivery_boy_id = $request->delivery_boy_id;

                $dboyLoc = new Trn_DeliveryBoyLocation;
                $dboyLoc->delivery_boy_id = $request->delivery_boy_id;
                $dboyLoc->latitude = $request->latitude;
                $dboyLoc->longitude = $request->longitude;

                if ($dboyLoc->save()) {
                    $data['status'] = 1;
                    $data['message'] = "success";
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Delivery boy doesn't Exist.";
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



    public function deliveryReport(Request $request)
    {
        try {

            if (isset($request->delivery_boy_id) && Mst_delivery_boy::find($request->delivery_boy_id)) {
                $delivery_boy_id = $request->delivery_boy_id;

                $deliveryReport = Trn_store_order::select(

                    'trn_store_orders.order_id',
                    'trn_store_orders.order_number',
                    'trn_store_orders.customer_id',
                    'trn_store_orders.store_id',
                    'trn_store_orders.subadmin_id',
                    'trn_store_orders.product_total_amount',
                    'trn_store_orders.delivery_charge',
                    'trn_store_orders.payment_type_id',

                    'trn_store_orders.status_id',

                    'trn_store_orders.delivery_boy_id',

                    'trn_store_orders.created_at',
                    'trn_store_orders.updated_at',

                    'trn_store_orders.delivery_date',
                    'trn_store_orders.delivery_time',

                    'trn_store_orders.order_type',

                    'trn_store_customers.customer_id',
                    'trn_store_customers.customer_first_name',
                    'trn_store_customers.customer_last_name',
                    'trn_store_customers.customer_mobile_number',
                    'trn_store_customers.place',

                    'mst_stores.store_id',
                    'mst_stores.store_name',
                    'mst_stores.store_mobile',

                    'mst_delivery_boys.delivery_boy_name',
                    'mst_delivery_boys.delivery_boy_mobile'



                )
                    ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
                    ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
                    ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id')
                    ->where('trn_store_orders.status_id', '=', 9);

                // $deliveryReport = $;

                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $deliveryReport = $deliveryReport->whereDate('trn_store_orders.delivery_date', '>=', $request->date_from);
                }

                if (isset($request->date_to)) {
                    $deliveryReport = $deliveryReport->whereDate('trn_store_orders.delivery_date', '<=', $request->date_to);
                }


                if (isset($request->customer_id)) {
                    $deliveryReport = $deliveryReport->where('trn_store_orders.customer_id', '=', $request->customer_id);
                }

                if (isset($request->delivery_boy_id)) {
                    $deliveryReport = $deliveryReport->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
                }

                if (isset($request->status_id)) {
                    $deliveryReport = $deliveryReport->where('trn_store_orders.status_id', '=', $request->status_id);
                }

                if (isset($request->order_type)) {
                    $deliveryReport = $deliveryReport->where('trn_store_orders.order_type', '=', $request->order_type);
                }


                $deliveryReport = $deliveryReport->where('trn_store_orders.delivery_boy_id', $delivery_boy_id)
                    ->orderBy('trn_store_orders.updated_at', 'DESC');


                if (isset($request->page)) {
                    $deliveryReport = $deliveryReport->paginate(20, ['data'], 'page', $request->page);
                } else {
                    $deliveryReport = $deliveryReport->paginate(20);
                }



                foreach ($deliveryReport as $sd) {
                    $sd->orderTotalDiscount = Helper::orderTotalDiscount($sd->order_id);
                    $sd->orderTotalTax = Helper::orderTotalTax($sd->order_id);

                    if ($sd->delivery_status_id == 1)
                        $sd->delivery_status =  'Assigned';
                    elseif ($sd->delivery_status_id == 2)
                        $sd->delivery_status =  'Inprogress';
                    elseif ($sd->delivery_status_id == 3)
                        $sd->delivery_status =  'Completed';
                    else
                        $sd->delivery_status =  '';

                    @$sd->status->status;

                    $sd->date = \Carbon\Carbon::parse($sd->created_at)->format('d-m-Y');

                    if (!isset($sd->customer_last_name))
                        $sd->customer_last_name = '';

                    if (!isset($sd->delivery_charge))
                        $sd->delivery_charge = '';

                    if (!isset($sd->coupon_code))
                        $sd->coupon_code = '';

                    if (!isset($sd->packing_charge))
                        $sd->packing_charge = '';

                    if (!isset($sd->payment_type_id))
                        $sd->payment_type_id = '';

                    if (!isset($sd->reward_points_used))
                        $sd->reward_points_used = '';

                    if (!isset($sd->amount_before_applying_rp))
                        $sd->amount_before_applying_rp = '';

                    if (!isset($sd->trn_id))
                        $sd->trn_id = '';

                    if (!isset($sd->amount_reduced_by_coupon))
                        $sd->amount_reduced_by_coupon = '';

                    if (!isset($sd->order_type))
                        $sd->order_type = '';

                    if (!isset($sd->customer_mobile_number))
                        $sd->customer_mobile_number = '';

                    if (!isset($sd->place))
                        $sd->place = '';
                }


                $data['deliveryReport'] = $deliveryReport;
                $data['status'] = 1;
                $data['message'] = "Success";
            } else {
                $data['status'] = 0;
                $data['message'] = "Store does not exist";
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
}
