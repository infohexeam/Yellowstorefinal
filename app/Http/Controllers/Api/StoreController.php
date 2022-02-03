<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Mst_store;
use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Mst_store_documents;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Trn_store_otp_verify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use DB;
use Carbon\Carbon;
use Crypt;
use Str;
use App\Models\admin\Mst_Video;
use App\Models\admin\Trn_StoreDeviceToken;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Trn_OrderSplitPayments;
use File;




class StoreController extends Controller
{

    public function storeVideoList(Request $request)
    {
        $data = array();
        try {

            $storeVids = Mst_Video::where('status', 1)->where('visibility', 1);
            if (isset($request->store_id)) {
                $storeTownData = Mst_store::find($request->store_id)->town_id;
                $storeVids = $storeVids->where('town_id', $storeTownData);
            }

            $storeVids = $storeVids->orderBy('video_id', 'DESC')->get();


            $data['videos'] = $storeVids;

            foreach ($data['videos'] as $v) {
                $linkCode = ' ';
                if ($v->platform == 'Youtube') {
                    $revLink = strrev($v->video_code);
                    $revLinkCode = substr($revLink, 0, strpos($revLink, '='));
                    $linkCode = strrev($revLinkCode);

                    if ($linkCode == "") {
                        $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                        $linkCode = strrev($revLinkCode);
                    }

                    //  echo $revLink." *** ".$linkCode." *** ".$linkCode." *** ".$v->video_code;die;
                }

                if (!isset($v->video_discription))
                    $v->video_discription = '';

                if ($v->platform == 'Vimeo') {
                    $revLink = strrev($v->video_code);
                    $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                    $linkCode = strrev($revLinkCode);

                    //  echo $revLink." *** ".$linkCode." *** ".$linkCode." *** ".$v->video_code;die;
                }

                $v->link_code = @$linkCode;

                if ($v->video_image) {
                    $v->video_image = '/assets/uploads/video_images/' . $v->video_image;
                } else {
                    $v->video_image =  Helper::default_video_image();
                }
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

    public function customerVideoList(Request $request)
    {
        $data = array();
        try {

            $cusVids = Mst_Video::where('status', 1)->where('visibility', 2);

            if (isset($request->customer_id)) {
                $cusTownId = Trn_store_customer::find($request->customer_id)->town_id;
                $cusVids = $cusVids->where('town_id', $cusTownId);
            }

            $cusVids = $cusVids->orderBy('video_id', 'DESC')->get();

            $data['videos'] = $cusVids;

            foreach ($data['videos'] as $v) {
                $linkCode = '';
                if ($v->platform == 'Youtube') {
                    $revLink = strrev($v->video_code);
                    $revLinkCode = substr($revLink, 0, strpos($revLink, '='));
                    $linkCode = strrev($revLinkCode);

                    if ($linkCode == "") {
                        $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                        $linkCode = strrev($revLinkCode);
                    }
                    //  echo $revLink." *** ".$linkCode." *** ".$linkCode." *** ".$v->video_code;die;
                }

                if (!isset($v->video_discription))
                    $v->video_discription = '';


                if ($v->platform == 'Vimeo') {
                    $revLink = strrev($v->video_code);
                    $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                    $linkCode = strrev($revLinkCode);

                    //  echo $revLink." *** ".$linkCode." *** ".$linkCode." *** ".$v->video_code;die;
                }

                if ($v->video_image) {
                    $v->video_image = '/assets/uploads/video_images/' . $v->video_image;
                } else {
                    $v->video_image =  Helper::default_video_image();
                }

                $v->link_code = @$linkCode;
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

    public function deliveryBoyVideoList(Request $request)
    {
        $data = array();
        try {

            $dboyVid = Mst_Video::where('status', 1)->where('visibility', 3);


            if (isset($request->delivery_boy_id)) {
                $dbTownId = Mst_delivery_boy::find($request->delivery_boy_id)->town_id;
                $dboyVid = $dboyVid->where('town_id', $dbTownId);
            }

            $dboyVid = $dboyVid->orderBy('video_id', 'DESC')->get();

            $data['videos'] = $dboyVid;

            foreach ($data['videos'] as $v) {
                $linkCode = '';
                if ($v->platform == 'Youtube') {
                    $revLink = strrev($v->video_code);
                    $revLinkCode = substr($revLink, 0, strpos($revLink, '='));
                    $linkCode = strrev($revLinkCode);

                    if ($linkCode == "") {
                        $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                        $linkCode = strrev($revLinkCode);
                    }
                    //  echo $revLink." *** ".$linkCode." *** ".$linkCode." *** ".$v->video_code;die;
                }

                if (!isset($v->video_discription))
                    $v->video_discription = '';

                if ($v->platform == 'Vimeo') {
                    $revLink = strrev($v->video_code);
                    $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                    $linkCode = strrev($revLinkCode);

                    //  echo $revLink." *** ".$linkCode." *** ".$linkCode." *** ".$v->video_code;die;
                }

                $v->link_code = @$linkCode;

                if ($v->video_image) {
                    $v->video_image = '/assets/uploads/video_images/' . $v->video_image;
                } else {
                    $v->video_image =  Helper::default_video_image();
                }
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



    public function VideoList(Request $request)
    {
        $data = array();
        try {

            $data['videos'] = Mst_Video::where('status', 1)->orderBy('video_id', 'DESC')->get();
            foreach ($data['videos'] as $v) {
                $linkCode = '';
                if ($v->platform == 'Youtube') {
                    $revLink = strrev($v->video_code);
                    $revLinkCode = substr($revLink, 0, strpos($revLink, '='));
                    $linkCode = strrev($revLinkCode);

                    //  echo $revLink." *** ".$linkCode." *** ".$linkCode." *** ".$v->video_code;die;
                }

                if ($v->platform == 'Vimeo') {
                    $revLink = strrev($v->video_code);
                    $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                    $linkCode = strrev($revLinkCode);

                    //  echo $revLink." *** ".$linkCode." *** ".$linkCode." *** ".$v->video_code;die;
                }


                $v->link_code = @$linkCode;
                if ($v->video_image) {
                    $v->video_image = '/assets/uploads/video_images/' . $v->video_image;
                } else {
                    $v->video_image =  Helper::default_video_image();
                }
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


    public function logout(Request $request)
    {
        // echo "here";die;
        $accessToken = auth()->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        //echo $token;die;
        $token->revoke();
        $data['status'] = 1;
        $data['message'] = "Success";
        return response($data);
    }

    public function logoutOtherDevice(Request $request)
    {

        $accessToken = auth()->user()->token();
        $token = $request->user()->tokens->find($accessToken);

        //dd($token->id);
        $divTok = DB::table('oauth_access_tokens')
            ->where('user_id', $token->user_id)
            ->whereNotIn('id', [$token->id])
            ->update(['revoked' => 1]);

        $data['status'] = 1;
        $data['message'] = "Success";
        return response($data);
    }


    public function logoutAllDevice(Request $request)
    {

        if (isset($request->store_id) && Mst_store::find($request->store_id)) {

            $divTok = DB::table('oauth_access_tokens')
                ->where('user_id', $request->store_id)
                ->update(['revoked' => 1]);

            $data['status'] = 1;
            $data['message'] = "Success";
        } else {
            $data['status'] = 0;
            $data['message'] = "Failed";
        }
        return response($data);
    }



    public function checkLoginStatus(Request $request)
    {

        $accessToken = auth()->user()->token();
        $token = $request->user()->tokens->find($accessToken);

        $divTok = DB::table('oauth_access_tokens')
            ->where('id', $token->id)
            ->where('revoked', 0)
            ->count();

        $divOne = DB::table('oauth_access_tokens')
            ->where('id', $token->id)
            ->where('revoked', 0)
            ->first();
        if ($divTok > 0) {
            $data['login_expired'] = 0;
        } else {
            $data['login_expired'] = 1;
        }

        $divTok2 = DB::table('oauth_access_tokens')
            ->where('user_id', $divOne->user_id)
            ->where('scopes', [])
            ->where('revoked', 0)
            ->count();
        $divTok2 = $divTok2 - 1;
        if ($divTok2 > 0) {
            $data['login_status '] = 1;
        } else {
            $data['login_status '] = 0;
        }


        $data['status'] = 1;
        $data['message'] = "Success";
        return response($data);
    }



    public function mobCheck(Request $request)
    {
        $data = array();
        try {
            $storMob = $request->store_mobile;
            if ($storMob) {

                if (Trn_StoreAdmin::where("store_mobile", '=', $storMob)->where('store_otp_verify_status', 0)->count() > 0) {
                    // $store_qrcodeData = Mst_store::where("store_mobile", '=', $storMob)->first();
                    // if (isset($store_qrcodeData->store_qrcode)) {
                    //     File::delete(public_path('upload/test.png')); // delete qrcode
                    // }
                    Trn_StoreAdmin::where("store_mobile", '=', $storMob)->delete();
                    Mst_store::where("store_mobile", '=', $storMob)->forceDelete();
                }

                $storMobCheck = Trn_StoreAdmin::where("store_mobile", '=', $storMob)->first();

                if ($storMobCheck) {
                    $data['status'] = 0;
                    $data['message'] = "Mobile Number Already in use";
                } else {
                    $data['status'] = 1;
                    $data['message'] = "Mobile Number Accepted";
                }
            } else {
                $data['status'] = 2;
                $data['message'] = "Mobile Number cannot be empty";
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


    public function nameCheck(Request $request)
    {
        $data = array();
        try {
            $storName = $request->store_name;
            if ($storName) {
                $storNameCheck = Trn_StoreAdmin::where("admin_name", '=', $storName)->first();
                if ($storNameCheck) {
                    $data['status'] = 0;
                    $data['message'] = "Store Name Already in use";
                } else {
                    $data['status'] = 1;
                    $data['message'] = "Store Name Accepted";
                }
            } else {
                $data['status'] = 2;
                $data['message'] = "Store Name cannot be empty";
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


    public function saveStore(Request $request, Mst_store $store, Mst_store_documents $store_doc, Trn_store_otp_verify $otp_verify)
    {
        $data = array();
        try {

            $validator = Helper::validateStore($request->all());
            if (!$validator->fails()) {
                $store->store_name            = $request->store_name;
                $store->store_name_slug       = Str::of($request->store_name)->slug('-');
                //	$store->store_contact_person_phone_number = $request->store_contact_person_phone_number;
                $store->store_mobile   = $request->store_mobile;
                $store->store_added_by        = 0;
                $store->password              = Hash::make($request->password);
                $store->store_account_status       = 0;
                $store->store_otp_verify_status    = 0;
                //   $store->store_contact_person_name            = $request->store_contact_person_name;
                //  $store->store_primary_address            = $request->store_contact_address;
                //   $store->store_country_id            = $request->store_country_id;
                //   $store->store_state_id            = $request->store_state_id;
                //   $store->store_district_id            = $request->store_district_id;
                //  $store->town_id            = $request->store_town_id;
                //   $store->place            = $request->store_place;
                $store->business_type_id            = $request->business_type_id;
                $store->store_username            = $request->store_mobile;
                $timestamp = time();
                $qrco = Str::of($request->store_name)->slug('-') . "-" . rand(10, 99) . "-" . @$request->store_mobile;

                \QrCode::format('svg')->size(500)->generate($qrco, 'assets/uploads/store_qrcodes/' . $qrco . '.svg');
                $store->store_qrcode          = $qrco;
                $store->subadmin_id          = 2; // default subadmin

                $store->latitude   = $request->latitude;
                $store->longitude   = $request->longitude;
                $store->place   = $request->place;

                $store->save();

                $store_id = DB::getPdo()->lastInsertId();

                $insert['store_id'] = $store_id;
                $insert['admin_name'] = $request->store_name;
                //  $insert['email'] = $request->email;
                $insert['username'] = $request->store_mobile;
                $insert['store_mobile'] = $request->store_mobile;
                $insert['role_id'] = 0;
                $insert['store_account_status'] = 0;
                $insert['expiry_date'] = Carbon::now()->addDays(30)->toDateString();

                $insert['password'] = Hash::make($request->password);
                $insert['subadmin_id'] = 0;

                Trn_StoreAdmin::create($insert);

                // if(isset($request->store_gst_number))
                // {
                //  $store_doc->store_id            = $store_id;
                //  $store_doc->store_document_gstin            = null;
                //   $store_doc->save();
                //}




                $store_otp =  5555;
                //$store_otp =  rand ( 1000 , 9999 );
                $store_otp_expirytime = Carbon::now()->addMinute(10);

                $otp_verify->store_id                 = $store_id;
                $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                $otp_verify->store_otp                 = $store_otp;
                $otp_verify->save();

                $data['store_id'] = $store_id;
                $data['otp'] = $store_otp;
                $data['status'] = 1;
                $data['message'] = "Store Registration Success";
            } else {
                $data['errors'] = $validator->errors();
                $data['status'] = 0;
                $data['message'] = "Store Registration Failed";
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


    public function loginStoreStatus(Request $request)
    {
        $data = array();
        try {
            $phone = $request->input('store_mobile');
            $passChk = $request->input('password');

            $validator = Validator::make(
                $request->all(),
                [
                    'store_mobile' => 'required',
                    'password' => 'required',
                ],
                [
                    'store_mobile.required' => "Store Mobile Number is required",
                    'password.required' => "Password is required",
                ]
            );
            if (!$validator->fails()) {
                $custCheck = Trn_StoreAdmin::where('store_mobile', '=', $phone)->first();
                $today = Carbon::now()->toDateString();

                if ($custCheck) {

                    if (Hash::check($passChk, $custCheck->password)) {
                        if (($custCheck->store_account_status != 0) || (($custCheck->store_account_status == 0) && ($today <= $custCheck->expiry_date))) {
                            if ($custCheck->store_otp_verify_status != 0) {
                                $data['status'] = 1;
                                $data['message'] = "Success";

                                $storeData = Mst_store::find($custCheck->store_id);

                                $data['online_status'] = $storeData->online_status;
                                $data['store_id'] = $custCheck->store_id;
                                $data['store_admin_id'] = $custCheck->store_admin_id;
                                $data['store_name'] = $storeData->store_name;
                                $data['store_username'] = $storeData->store_username;
                                $data['store_mobile'] = $phone;
                                $data['password'] = $passChk;

                                $divTok = DB::table('oauth_access_tokens')
                                    ->where('user_id', $custCheck->store_admin_id)
                                    ->where('scopes', [])
                                    ->where('revoked', 0)
                                    ->count();

                                if ($divTok > 0) {
                                    $data['login_status '] = 1;
                                } else {
                                    $data['login_status '] = 0;
                                }
                            } else {
                                $data['status'] = 2;
                                $data['message'] = "OTP not verified";
                                $storeData = Mst_store::find($custCheck->store_id);

                                $data['store_id'] = $custCheck->store_id;
                                $data['store_admin_id'] = $custCheck->store_admin_id;
                                $data['store_name'] = $storeData->store_name;
                                $data['store_mobile'] = $phone;
                            }
                        } else {

                            if ($custCheck->store_account_status == 0) {
                                $data['status'] = 4;
                                $data['message'] = "Store is Inactive. Please contact Super admin";
                            } else {
                                $data['status'] = 4;
                                $data['message'] = "Profile not Activated";
                            }
                        }
                    } else {
                        $data['status'] = 3;
                        $data['message'] = "Mobile Number or Password is Invalid";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Login Details";
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


    public function loginStore(Request $request)
    {
        $data = array();
        try {
            $phone = $request->input('store_mobile');
            $passChk = $request->input('password');
            // $devType = $request->input('device_type');
            //    $devToken = $request->input('device_token');

            $validator = Validator::make(
                $request->all(),
                [
                    'store_mobile' => 'required',
                    'password' => 'required',
                    // 'device_type' => 'required',
                    // 'device_token' => 'required',
                ],
                [
                    'store_mobile.required' => "Store Mobile Number is required",
                    'password.required' => "Password is required",
                    // 'device_type.required' => "Device Type is required",
                    // 'device_toke.required' => "Device Token is required",
                ]
            );
            if (!$validator->fails()) {
                $custCheck = Trn_StoreAdmin::where('store_mobile', '=', $phone)->first();
                $today = Carbon::now()->toDateString();

                if ($custCheck) {

                    if (Hash::check($passChk, $custCheck->password)) {
                        if (($custCheck->store_account_status != 0) || (($custCheck->store_account_status == 0) && ($today <= $custCheck->expiry_date))) {
                            if ($custCheck->store_otp_verify_status != 0) {
                                $data['status'] = 1;
                                $data['message'] = "Login Success";

                                DB::table('oauth_access_tokens')->where('user_id', $custCheck->store_admin_id)->update(['revoked' => 1]);

                                $storeData = Mst_store::find($custCheck->store_id);
                                // $storeData->online_status = 1;
                                // $storeData->update();

                                $data['online_status'] = $storeData->online_status;
                                $data['store_id'] = $custCheck->store_id;
                                $data['store_admin_id'] = $custCheck->store_admin_id;

                                $dataName = '';
                                if ($custCheck->role_id != 0)
                                    $dataName =   Trn_StoreAdmin::find($custCheck->store_admin_id)->admin_name;



                                $data['store_name'] = $storeData->store_name;
                                $data['store_admin_name'] = $dataName;
                                $data['store_username'] = $storeData->store_username;
                                $data['access_token'] = $custCheck->createToken('authToken')->accessToken;

                                $divTok = DB::table('oauth_access_tokens')
                                    ->where('user_id', $custCheck->store_admin_id)
                                    ->where('scopes', [])
                                    ->where('revoked', 0)
                                    ->count();
                                $divTok = $divTok - 1;
                                if ($divTok > 0) {
                                    $data['login_status '] = 1;
                                } else {
                                    $data['login_status '] = 0;
                                }

                                if (isset($request->device_token) && isset($request->device_type)) {
                                    Trn_StoreDeviceToken::where('store_id', $custCheck->store_id)
                                        ->where('store_admin_id', $custCheck->store_admin_id)
                                        // ->where('store_device_token',$request->device_token)
                                        ->delete();

                                    $cdt = new Trn_StoreDeviceToken;
                                    $cdt->store_id = $custCheck->store_id;
                                    $cdt->store_admin_id = $custCheck->store_admin_id;
                                    $cdt->store_device_token = $request->device_token;
                                    $cdt->store_device_type = $request->device_type;
                                    $cdt->save();
                                }
                            } else {
                                $storeData = Mst_store::find($custCheck->store_id);
                                $data['store_id'] = $custCheck->store_id;
                                $data['store_admin_id'] = $custCheck->store_admin_id;
                                $data['store_name'] = $storeData->store_name;
                                $data['status'] = 2;
                                $data['message'] = "OTP not verified";
                            }
                        } else {

                            if ($custCheck->store_account_status == 0) {
                                $data['status'] = 4;
                                $data['message'] = "Store is Inactive. Please contact Super admin";
                            } else {
                                $data['status'] = 4;
                                $data['message'] = "Profile not Activated";
                            }
                        }
                    } else {
                        $data['status'] = 3;
                        $data['message'] = "Mobile Number or Password is Invalid";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Login Details";
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


    public function verifyOtp(Request $request)
    {
        $data = array();
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $otp = $request->otp_status;
                //	$otp = $request->store_otp; //old
                if ($otp == 'accepted') {
                    $storeId = $request->store_id;

                    $store['store_account_status'] = 1;

                    $store['store_otp_verify_status'] = 1;

                    if (Trn_StoreAdmin::where('store_id', $storeId)->where('role_id', 0)->update($store)) {
                        $data['status'] = 1;
                        $data['message'] = "success";
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "store not found";
            }

            //     $otp_verify =  Trn_store_otp_verify::where('store_id', '=', $storeId)->latest()->first();

            // 	if($otp_verify)
            //   		{
            // 			$store_otp_exp_time = $otp_verify->store_otp_expirytime;
            // 			$current_time = Carbon::now()->toDateTimeString();
            //     		$store_new_otp =  $otp_verify->store_otp;

            //     		 if($store_new_otp == $request->store_otp)
            //         		{
            //           if($current_time < $store_otp_exp_time)
            //           {
            //               			//$store = Trn_StoreAdmin::Find($storeId);
            //               $store['store_account_status'] = 1;
            //               $store['store_otp_verify_status'] = 1;
            //               Trn_StoreAdmin::where('store_id',$storeId)->where('role_id',0)->update($store);

            //               			$data['status'] = 1;
            //                         $data['message'] = "OTP Verifiction Success";

            //             		} else{
            //             			$data['status'] = 2;
            //                         $data['message'] = "OTP expired.click on resend OTP";	
            //             		}

            //         			}else{
            //         				$data['status'] = 3;
            //                         $data['message'] = "Incorrect OTP entered. Please enter a valid OTP.";
            //         			}
            //     				}else{
            //     					$data['status'] = 3;
            //                         $data['message'] = "Store OTP not found. Please click on resend OTP.";
            //     				}


            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }


    public function resendOtp(Request $request, Mst_store $store, Trn_store_otp_verify $otp_verify)
    {
        $data = array();
        try {
            $store_id = $request->store_id;
            $otp_verify = Trn_store_otp_verify::where('store_id', '=', $store_id)->latest()->first();
            if ($otp_verify) {

                if ($otp_verify !== null) {
                    $store_otp_verify_id = $otp_verify->store_otp_verify_id;
                    $otp_verify = Trn_store_otp_verify::Find($store_otp_verify_id);
                    $extented_time = Carbon::now()->addMinute(10);
                    $otp_verify->store_otp_expirytime = $extented_time;
                    $otp_verify->update();
                    $data['status'] = 1;
                    $data['otp'] = $otp_verify->store_otp;
                    $data['message'] = "OTP resent Success.";
                } else {
                    $otp_verify = new Trn_store_otp_verify;
                    $store_otp =  5555;
                    //$store_otp =  rand ( 1000 , 9999 );
                    $store_otp_expirytime = Carbon::now()->addMinute(10);
                    $otp_verify->store_id                 = $store_id;
                    $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                    $otp_verify->store_otp                 = $store_otp;
                    $otp_verify->save();
                    $data['status'] = 2;
                    $data['message'] = "OTP registerd successfully. Please verify OTP.";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Store Doesn't Exist.";
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




    public function FpverifyMobile(Request $request, Trn_store_otp_verify $otp_verify)
    {
        $data = array();
        try {
            // 	$mobCode = $request->country_code;

            $mobNumber = $request->store_mobile;
            $mobCheck = Trn_StoreAdmin::where("store_mobile", '=', $mobNumber)->latest()->first();

            if ($mobCheck) {

                $store_id = $mobCheck->store_id;
                $store_mob = $mobCheck->store_mobile;

                $validator = Validator::make(
                    $request->all(),
                    [
                        // 'country_code' => 'required',
                        'store_mobile' => 'required'
                    ],
                    [
                        // 'country_code.required' => "Country Code is required",
                        'store_mobile.required' => "Mobile Number is required",


                    ]
                );

                if (!$validator->fails()) {
                    // $store_otp =  rand ( 1000 , 9999 );
                    $store_otp =  5555;
                    $store_otp_expirytime = Carbon::now()->addMinute(10);

                    $otp_verify->store_id                 = $store_id;
                    $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                    $otp_verify->store_otp                = $store_otp;
                    $otp_verify->save();

                    $data['status'] = 1;
                    $data['store_id'] = $store_id;
                    $data['store_mobile'] = $store_mob;
                    $data['store_otp'] = $store_otp;
                    $data['message'] = "Mobile Verification Success. OTP Sent to registered mobile number";
                } else {

                    $data['status'] = 0;
                    $data['errors'] = $validator->errors();
                    $data['message'] = "Verification Failed";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Store Does not exist";
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

    public function FpverifyOTP(Request $request, Trn_store_otp_verify $otp_verify)
    {
        $data = array();
        try {
            $otp = $request->store_otp;
            // $mobCode = $request->country_code;
            $mobNumber = $request->store_mobile;

            $mobCheck = Trn_StoreAdmin::where("store_mobile", '=', $mobNumber)->latest()->first();
            if ($mobCheck) {
                $store_id = $mobCheck->store_id;
                $store_mob = $mobCheck->store_mobile;
                $otpCheck = Trn_store_otp_verify::where('store_id', '=', $store_id)->where('store_otp', '=', $otp)->latest()->first();

                if ($otpCheck) {
                    $store_otp_exp_time = $otpCheck->store_otp_expirytime;
                    $current_time = Carbon::now()->toDateTimeString();
                    $store_new_otp =  $otpCheck->store_otp;

                    // $expParse = $expTime->format('Y-m-d H:i:s');

                    if ($current_time < $store_otp_exp_time) {

                        $data['status'] = 1;
                        $data['store_id'] = $store_id;
                        $data['store_mobile'] = $store_mob;
                        $data['message'] = "OTP verification success. Enter a new password.";
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
                $data['message'] = "Store Does not exist";
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
            // 	$mobCode = $request->country_code;
            $store_id = $request->store_id;
            $mobNumber = $request->store_mobile;
            $mobCheck = Trn_StoreAdmin::where("store_mobile", '=', $mobNumber)->where('store_id', '=', $store_id)->first();

            if ($mobCheck) {
                // 			$custId = $mobCheck->store_id;
                // 			$fetchCustDet = Main_customer::where('customer_id','=',$custId)->first();
                $validator = Validator::make($request->all(), [
                    'password' => 'required|string|min:8|confirmed'
                ]);

                if (!$validator->fails()) {
                    $encPass = Hash::make($request->input('password'));
                    Trn_StoreAdmin::where('store_id', $store_id)->where("store_mobile", '=', $mobNumber)->update(['password' => $encPass]);
                    Mst_store::where('store_id', $store_id)->where("store_mobile", '=', $mobNumber)->update(['password' => $encPass]);
                    $data['status'] = "1";
                    $data['messsage'] = "Password Changed successfully";
                } else {
                    $data['status'] = "0";
                    $data['errors'] = $validator->errors();
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Store Does not exist";
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

    public function onlineStatus(Request $request)
    {
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $storeData = Mst_store::find($request->store_id);
                $storeData->online_status = $request->status;
                if ($storeData->update()) {
                    $data['status'] = 1;
                    $data['online_status'] = $request->status;
                    if ($request->status == 1) {
                        $data['message'] = "Store online";
                    } else {
                        $data['message'] = "Store offline";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Store Does not exist";
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


    public function getLoginOnlineStatus(Request $request)
    {
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                //dd(auth()->user()->token());
                if ($accessToken = auth()->user()->token()) {
                    $token =  $request->user()->tokens->find($accessToken);

                    $divTok = DB::table('oauth_access_tokens')
                        ->where('id', $token->id)
                        ->first();

                    if ($divTok->revoked == 1) {
                        $data['login_status'] = 0;
                        $data['message'] = "Token expired";
                    } else {
                        $data['login_status'] = 1;
                        $data['message'] = "Success";
                    }
                } else {
                    $data['message'] = "Success";
                }

                $data['message'] = "Success";

                $statusStore = Mst_store::select('online_status')->where('store_id', $request->store_id)->first();
                $custCheck = Trn_StoreAdmin::where('store_id', '=', $request->store_id)->where('role_id', 0)->first();
                $today = Carbon::now()->toDateString();
                if (($custCheck->store_account_status != 0) || (($custCheck->store_account_status == 0) && ($today <= $custCheck->expiry_date))) {
                    $data['store_status'] = 1;
                } else {
                    $data['store_status'] = 0;
                }




                $data['online_status'] = $statusStore->online_status;
                $data['status'] = 1;
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

    public function getOnlineStatus(Request $request)
    {
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $statusStore = Mst_store::select('online_status')->where('store_id', $request->store_id)->first();
                $custCheck = Trn_StoreAdmin::where('store_id', '=', $request->store_id)->where('role_id', 0)->first();
                $today = Carbon::now()->toDateString();
                if (($custCheck->store_account_status != 0) || (($custCheck->store_account_status == 0) && ($today <= $custCheck->expiry_date))) {
                    $data['store_status'] = 1;
                } else {
                    $data['store_status'] = 0;
                }

                $data['online_status'] = $statusStore->online_status;
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



    public function salesReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $salesData = Trn_store_order::select(

                    'trn_store_orders.order_id',
                    'trn_store_orders.order_number',
                    'trn_store_orders.customer_id',
                    'trn_store_orders.store_id',
                    'trn_store_orders.subadmin_id',
                    'trn_store_orders.product_total_amount',
                    'trn_store_orders.delivery_charge',
                    'trn_store_orders.packing_charge',
                    'trn_store_orders.payment_type_id',
                    'trn_store_orders.status_id',
                    'trn_store_orders.payment_status',
                    'trn_store_orders.delivery_status_id',
                    'trn_store_orders.delivery_boy_id',
                    'trn_store_orders.coupon_id',
                    'trn_store_orders.coupon_code',
                    'trn_store_orders.reward_points_used',
                    'trn_store_orders.amount_before_applying_rp',
                    'trn_store_orders.trn_id',
                    'trn_store_orders.created_at',
                    'trn_store_orders.amount_reduced_by_coupon',
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
                    ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
                    ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
                    ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');


                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $salesData = $salesData->whereDate('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $salesData = $salesData->whereDate('trn_store_orders.created_at', '<=', $a2);
                }

                if (isset($request->customer_id)) {
                    $salesData = $salesData->where('trn_store_orders.customer_id', '=', $request->customer_id);
                }
 
                if (isset($request->delivery_boy_id)) {
                    $salesData = $salesData->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
                }

                if (isset($request->status_id)) {
                    $salesData = $salesData->where('trn_store_orders.status_id', '=', $request->status_id);
                }

                if (isset($request->order_type)) {
                    $salesData = $salesData->where('trn_store_orders.order_type', '=', $request->order_type);
                }


                $salesData = $salesData->where('trn_store_orders.store_id', $request->store_id)
                    ->orderBy('trn_store_orders.order_id', 'DESC');

                if (isset($request->page)) {
                    $salesData = $salesData->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $salesData = $salesData->paginate(10);
                }


                foreach ($salesData as $sd) {
                    $sd->orderTotalDiscount = Helper:   :orderTotalDiscount($sd->order_id);
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

                $data['salesData'] = $salesData;
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

    public function salesOnlineReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $salesData = Trn_store_order::select(

                    'trn_store_orders.order_id',
                    'trn_store_orders.order_number',
                    'trn_store_orders.customer_id',
                    'trn_store_orders.store_id',
                    'trn_store_orders.subadmin_id',
                    'trn_store_orders.product_total_amount',
                    'trn_store_orders.delivery_charge',
                    'trn_store_orders.packing_charge',
                    'trn_store_orders.payment_type_id',
                    'trn_store_orders.status_id',
                    'trn_store_orders.payment_status',
                    'trn_store_orders.delivery_status_id',
                    'trn_store_orders.delivery_boy_id',
                    'trn_store_orders.coupon_id',
                    'trn_store_orders.coupon_code',
                    'trn_store_orders.reward_points_used',
                    'trn_store_orders.amount_before_applying_rp',
                    'trn_store_orders.trn_id',
                    'trn_store_orders.created_at',
                    'trn_store_orders.amount_reduced_by_coupon',
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
                    ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');


                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $salesData = $salesData->whereDate('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $salesData = $salesData->whereDate('trn_store_orders.created_at', '<=', $a2);
                }


                if (isset($request->customer_id)) {
                    $salesData = $salesData->where('trn_store_orders.customer_id', '=', $request->customer_id);
                }

                if (isset($request->delivery_boy_id)) {
                    $salesData = $salesData->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
                }

                if (isset($request->status_id)) {
                    $salesData = $salesData->where('trn_store_orders.status_id', '=', $request->status_id);
                }

                if (isset($request->order_type)) {
                    $salesData = $salesData->where('trn_store_orders.order_type', '=', $request->order_type);
                }

                $salesData = $salesData->where('trn_store_orders.store_id', $request->store_id)->where('trn_store_orders.order_type', 'APP')
                    ->orderBy('trn_store_orders.order_id', 'DESC');

                if (isset($request->page)) {
                    $salesData = $salesData->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $salesData = $salesData->paginate(10);
                }

                foreach ($salesData as $sd) {
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

                $data['salesData'] = $salesData;
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



    public function salesOfflineReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $salesData = Trn_store_order::select(

                    'trn_store_orders.order_id',
                    'trn_store_orders.order_number',
                    'trn_store_orders.customer_id',
                    'trn_store_orders.store_id',
                    'trn_store_orders.subadmin_id',
                    'trn_store_orders.product_total_amount',
                    'trn_store_orders.delivery_charge',
                    'trn_store_orders.packing_charge',
                    'trn_store_orders.payment_type_id',
                    'trn_store_orders.status_id',
                    'trn_store_orders.payment_status',
                    'trn_store_orders.delivery_status_id',
                    'trn_store_orders.delivery_boy_id',
                    'trn_store_orders.coupon_id',
                    'trn_store_orders.coupon_code',
                    'trn_store_orders.reward_points_used',
                    'trn_store_orders.amount_before_applying_rp',
                    'trn_store_orders.trn_id',
                    'trn_store_orders.created_at',
                    'trn_store_orders.amount_reduced_by_coupon',
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
                    ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');


                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $salesData = $salesData->whereDate('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $salesData = $salesData->whereDate('trn_store_orders.created_at', '<=', $a2);
                }


                if (isset($request->customer_id)) {
                    $salesData = $salesData->where('trn_store_orders.customer_id', '=', $request->customer_id);
                }

                if (isset($request->delivery_boy_id)) {
                    $salesData = $salesData->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
                }

                if (isset($request->status_id)) {
                    $salesData = $salesData->where('trn_store_orders.status_id', '=', $request->status_id);
                }

                if (isset($request->order_type)) {
                    $salesData = $salesData->where('trn_store_orders.order_type', '=', $request->order_type);
                }


                $salesData = $salesData->where('trn_store_orders.store_id', $request->store_id)->where('trn_store_orders.order_type', 'POS')
                    ->orderBy('trn_store_orders.order_id', 'DESC');


                if (isset($request->page)) {
                    $salesData = $salesData->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $salesData = $salesData->paginate(10);
                }


                foreach ($salesData as $sd) {
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

                $data['salesData'] = $salesData;
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



    public function inventoryReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;

                $inventoryData =   Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                    ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                    ->leftjoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
                    ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
                    ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')

                    ->where('mst_store_products.store_id', $store_id)
                    ->where('mst_store_products.product_type', 1)
                    // ->orderBy('mst_store_products.product_name','ASC')
                    ->orderBy('mst_store_product_varients.stock_count', 'ASC')

                    ->select(
                        'mst_store_products.product_id',
                        'mst_store_products.product_name',
                        'mst_store_products.product_code',
                        'mst_store_products.product_cat_id',
                        'mst_store_products.product_base_image',
                        'mst_store_products.product_status',
                        'mst_store_products.product_brand',
                        'mst_store_products.min_stock',

                        'mst_store_products.tax_id',
                        'mst_store_product_varients.product_varient_id',
                        'mst_store_product_varients.variant_name',
                        'mst_store_product_varients.product_varient_price',
                        'mst_store_product_varients.product_varient_offer_price',
                        'mst_store_product_varients.product_varient_base_image',
                        'mst_store_product_varients.stock_count',
                        'mst_store_product_varients.created_at',
                        'mst_store_categories.category_id',
                        'mst_store_categories.category_name',
                        'mst__stock_details.stock',
                        'mst__stock_details.prev_stock',
                        'mst__stock_details.created_at AS updated_time',
                        'mst_store_agencies.agency_name',
                        'mst__sub_categories.sub_category_name',

                    );



                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                // if(isset($request->date_from))
                // {
                //   $inventoryData = $inventoryData->whereDate('trn_store_orders.created_at','>=',$a1);
                // }

                // if(isset($request->date_to))
                // {
                //   $inventoryData = $inventoryData->whereDate('trn_store_orders.created_at','<=',$a2);
                // }

                if (isset($request->product_id)) {
                    $inventoryData = $inventoryData->where('mst_store_products.product_id', $request->product_id);
                }

                if (isset($request->agency_id)) {
                    $inventoryData = $inventoryData->where('mst_store_agencies.agency_id', $request->agency_id);
                }

                if (isset($request->category_id)) {
                    $inventoryData = $inventoryData->where('mst_store_categories.category_id', $request->category_id);
                }

                if (isset($request->sub_category_id)) {
                    $inventoryData = $inventoryData->where('mst__sub_categories.sub_category_id', $request->sub_category_id);
                }



                $inventoryData = $inventoryData->orderBy('mst__stock_details.stock_detail_id', 'DESC');

                if (isset($request->page)) {
                    $inventoryData = $inventoryData->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $inventoryData = $inventoryData->paginate(10);
                }

                //  $inventoryData = $inventoryData->groupBy('mst_store_product_varients.product_varient_id');
                // $dataArr  = array();
                // foreach($inventoryData as $d)
                // {
                //     $dataArr[] = $d;
                // }


                $inventoryData = collect($inventoryData);
                $inventoryDatas = $inventoryData->unique('product_varient_id');
                $dataReViStoreSS =   $inventoryDatas->values()->all();

                $data['inventoryData'] = $inventoryData;
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


    public function outOffStockReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;

                $inventoryData =  Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                    ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                    ->leftjoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
                    ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
                    ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')

                    ->where('mst_store_products.store_id', $store_id)
                    ->where('mst_store_product_varients.stock_count', '<=', 0)
                    ->where('mst_store_products.product_type', 1)
                    // ->orderBy('mst_store_products.product_name','ASC')
                    ->orderBy('mst_store_product_varients.stock_count', 'ASC')

                    ->select(
                        'mst_store_products.product_id',
                        'mst_store_products.product_name',
                        'mst_store_products.product_code',
                        'mst_store_products.product_cat_id',
                        'mst_store_products.product_base_image',
                        'mst_store_products.product_status',
                        'mst_store_products.product_brand',
                        'mst_store_products.min_stock',

                        'mst_store_products.tax_id',
                        'mst_store_product_varients.product_varient_id',
                        'mst_store_product_varients.variant_name',
                        'mst_store_product_varients.product_varient_price',
                        'mst_store_product_varients.product_varient_offer_price',
                        'mst_store_product_varients.product_varient_base_image',
                        'mst_store_product_varients.stock_count',
                        'mst_store_product_varients.created_at',
                        'mst_store_categories.category_id',
                        'mst_store_categories.category_name',
                        'mst__stock_details.stock',
                        'mst__stock_details.prev_stock',
                        'mst__stock_details.created_at AS updated_time',
                        'mst_store_agencies.agency_name',
                        'mst__sub_categories.sub_category_name',

                    );


                $datefrom = $request->date_from;
                $dateto = $request->date_to;

                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                // if(isset($request->date_from))
                // {
                //   $inventoryData = $inventoryData->whereDate('trn_store_orders.created_at','>=',$a1);
                // }

                // if(isset($request->date_to))
                // {
                //   $inventoryData = $inventoryData->whereDate('trn_store_orders.created_at','<=',$a2);
                // }

                if (isset($request->product_id)) {
                    $inventoryData = $inventoryData->where('mst_store_products.product_id', $request->product_id);
                }

                if (isset($request->agency_id)) {
                    $inventoryData = $inventoryData->where('mst_store_agencies.agency_id', $request->agency_id);
                }

                if (isset($request->category_id)) {
                    $inventoryData = $inventoryData->where('mst_store_categories.category_id', $request->category_id);
                }

                if (isset($request->sub_category_id)) {
                    $inventoryData = $inventoryData->where('mst__sub_categories.sub_category_id', $request->sub_category_id);
                }

                $inventoryData = $inventoryData->groupBy('product_varient_id');

                if (isset($request->page)) {
                    $inventoryData = $inventoryData->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $inventoryData = $inventoryData->paginate(10);
                }



                // $inventoryData = $inventoryData->get();



                // $inventoryData = collect($inventoryData);
                //         $inventoryDatas = $inventoryData->unique('product_varient_id');
                //           $dataReViStoreSS =   $inventoryDatas->values()->all();




                $data['inventoryData'] = $inventoryData;

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


    public function paymentReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;

                $paymentReport =  Trn_store_order::select(

                    'trn_store_orders.order_id',
                    'trn_store_orders.order_number',
                    'trn_store_orders.customer_id',
                    'trn_store_orders.store_id',
                    'trn_store_orders.subadmin_id',
                    'trn_store_orders.product_total_amount',
                    'trn_store_orders.delivery_charge',
                    'trn_store_orders.packing_charge',
                    'trn_store_orders.payment_type_id',
                    'trn_store_orders.payment_status',
                    'trn_store_orders.trn_id',

                    'trn_store_orders.status_id',
                    'trn_store_orders.payment_status',
                    'trn_store_orders.delivery_status_id',
                    'trn_store_orders.delivery_boy_id',
                    'trn_store_orders.coupon_id',
                    'trn_store_orders.coupon_code',
                    'trn_store_orders.reward_points_used',
                    'trn_store_orders.amount_before_applying_rp',
                    'trn_store_orders.trn_id',
                    'trn_store_orders.created_at',
                    'trn_store_orders.amount_reduced_by_coupon',
                    'trn_store_orders.order_type',

                    'trn_store_customers.customer_id',
                    'trn_store_customers.customer_first_name',
                    'trn_store_customers.customer_last_name',
                    'trn_store_customers.customer_mobile_number',
                    'trn_store_customers.place',

                    'mst_stores.store_id',
                    'mst_stores.store_name',
                    'mst_stores.store_mobile',

                    'trn__order_payment_transactions.referenceId',

                    'mst_delivery_boys.delivery_boy_name',
                    'mst_delivery_boys.delivery_boy_mobile'



                )
                    ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
                    ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
                    ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id')

                    ->leftjoin('trn__order_payment_transactions', 'trn__order_payment_transactions.order_id', '=', 'trn_store_orders.order_id');



                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $paymentReport = $paymentReport->whereDate('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $paymentReport = $paymentReport->whereDate('trn_store_orders.created_at', '<=', $a2);
                }


                if (isset($request->customer_id)) {
                    $paymentReport = $paymentReport->where('trn_store_orders.customer_id', '=', $request->customer_id);
                }

                if (isset($request->delivery_boy_id)) {
                    $paymentReport = $paymentReport->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
                }

                if (isset($request->status_id)) {
                    $paymentReport = $paymentReport->where('trn_store_orders.status_id', '=', $request->status_id);
                }

                if (isset($request->order_type)) {
                    $paymentReport = $paymentReport->where('trn_store_orders.order_type', '=', $request->order_type);
                }


                $paymentReport = $paymentReport->where('trn_store_orders.store_id', $store_id)->where('trn_store_orders.order_type', 'APP')
                    ->orderBy('trn_store_orders.order_id', 'DESC');


                if (isset($request->page)) {
                    $paymentReport = $paymentReport->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $paymentReport = $paymentReport->paginate(10);
                }



                foreach ($paymentReport as $sd) {
                    $sd->orderTotalDiscount = Helper::orderTotalDiscount($sd->order_id);
                    $sd->orderTotalTax = Helper::orderTotalTax($sd->order_id);

                    $sd->trn_id = $sd->referenceId;

                    if ($sd->delivery_status_id == 1)
                        $sd->delivery_status =  'Assigned';
                    elseif ($sd->delivery_status_id == 2)
                        $sd->delivery_status =  'Inprogress';
                    elseif ($sd->delivery_status_id == 3)
                        $sd->delivery_status =  'Completed';
                    else
                        $sd->delivery_status =  '';

                    @$sd->status->status;

                    if ($sd->payment_type_id == 1)
                        $sd->payment_type = 'COD';
                    else
                        $sd->payment_type = 'Online';


                    if (($sd->payment_type_id == 2) && ($sd->status_id == 4 || $sd->status_id > 5))
                        $sd->payment_status = 'Success';
                    else
                        $sd->payment_status = '--';



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

                $data['paymentReport'] = $paymentReport;
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





    public function incomingPaymentReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;



                $paymentReport =  Trn_store_order::select(

                    'trn_store_orders.order_id',
                    'trn_store_orders.order_number',
                    'trn_store_orders.customer_id',
                    'trn_store_orders.store_id',
                    'trn_store_orders.subadmin_id',
                    'trn_store_orders.product_total_amount',
                    'trn_store_orders.delivery_charge',
                    'trn_store_orders.packing_charge',
                    'trn_store_orders.payment_type_id',
                    'trn_store_orders.payment_status',
                    'trn_store_orders.trn_id',

                    'trn_store_orders.status_id',
                    'trn_store_orders.payment_status',
                    'trn_store_orders.delivery_status_id',
                    'trn_store_orders.delivery_boy_id',
                    'trn_store_orders.coupon_id',
                    'trn_store_orders.coupon_code',
                    'trn_store_orders.reward_points_used',
                    'trn_store_orders.amount_before_applying_rp',
                    'trn_store_orders.trn_id',
                    'trn_store_orders.created_at',
                    'trn_store_orders.amount_reduced_by_coupon',
                    'trn_store_orders.order_type',

                    'trn_store_customers.customer_id',
                    'trn_store_customers.customer_first_name',
                    'trn_store_customers.customer_last_name',
                    'trn_store_customers.customer_mobile_number',
                    'trn_store_customers.place',

                    'mst_stores.store_id',
                    'mst_stores.store_name',
                    'mst_stores.store_mobile',

                    'trn__order_payment_transactions.referenceId',

                    'mst_delivery_boys.delivery_boy_name',
                    'mst_delivery_boys.delivery_boy_mobile'



                )
                    ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
                    ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
                    ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id')

                    ->join('trn__order_payment_transactions', 'trn__order_payment_transactions.order_id', '=', 'trn_store_orders.order_id');



                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $paymentReport = $paymentReport->whereDate('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $paymentReport = $paymentReport->whereDate('trn_store_orders.created_at', '<=', $a2);
                }


                if (isset($request->customer_id)) {
                    $paymentReport = $paymentReport->where('trn_store_orders.customer_id', '=', $request->customer_id);
                }

                if (isset($request->delivery_boy_id)) {
                    $paymentReport = $paymentReport->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
                }

                if (isset($request->status_id)) {
                    $paymentReport = $paymentReport->where('trn_store_orders.status_id', '=', $request->status_id);
                }

                if (isset($request->order_type)) {
                    $paymentReport = $paymentReport->where('trn_store_orders.order_type', '=', $request->order_type);
                }


                $paymentReport = $paymentReport->where('trn_store_orders.store_id', $store_id)->where('trn_store_orders.order_type', 'APP')
                    ->orderBy('trn_store_orders.order_id', 'DESC');


                if (isset($request->page)) {
                    $paymentReport = $paymentReport->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $paymentReport = $paymentReport->paginate(10);
                }



                foreach ($paymentReport as $sd) {
                    $sd->orderTotalDiscount = Helper::orderTotalDiscount($sd->order_id);
                    $sd->orderTotalTax = Helper::orderTotalTax($sd->order_id);

                    $sd->trn_id = $sd->referenceId;

                    if ($sd->delivery_status_id == 1)
                        $sd->delivery_status =  'Assigned';
                    elseif ($sd->delivery_status_id == 2)
                        $sd->delivery_status =  'Inprogress';
                    elseif ($sd->delivery_status_id == 3)
                        $sd->delivery_status =  'Completed';
                    else
                        $sd->delivery_status =  '';

                    @$sd->status->status;

                    if ($sd->payment_type_id == 1)
                        $sd->payment_type = 'COD';
                    else
                        $sd->payment_type = 'Online';


                    if (($sd->payment_type_id == 2) && ($sd->status_id == 4 || $sd->status_id > 5))
                        $sd->payment_status = 'Success';
                    else
                        $sd->payment_status = '--';



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

                    $sd->orderPaymentTransaction = new \stdClass();
                    $opt = Trn_OrderPaymentTransaction::where('order_id', $sd->order_id)->get();
                    $optConunt = Trn_OrderPaymentTransaction::where('order_id', $sd->order_id)->count();
                    if ($optConunt > 0) {
                        foreach ($opt as $row) {
                            $ospCount = Trn_OrderSplitPayments::where('opt_id', $row->opt_id)->count();
                            if ($ospCount > 0) {
                                $osp = Trn_OrderSplitPayments::where('opt_id', $row->opt_id)->where('paymentRole', 1)->get();
                                $row->orderSplitPayments = $osp;
                            }
                        }
                    }
                    //Trn_OrderPaymentTransaction
                    $sd->orderPaymentTransaction = $opt;
                }

                $data['paymentReport'] = $paymentReport;
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


    public function deliveryReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;

                $deliveryReport = Trn_store_order::select(

                    'trn_store_orders.order_id',
                    'trn_store_orders.order_number',
                    'trn_store_orders.customer_id',
                    'trn_store_orders.store_id',
                    'trn_store_orders.subadmin_id',
                    'trn_store_orders.product_total_amount',
                    'trn_store_orders.delivery_charge',
                    'trn_store_orders.packing_charge',
                    'trn_store_orders.payment_type_id',
                    'trn_store_orders.status_id',
                    'trn_store_orders.payment_status',
                    'trn_store_orders.delivery_status_id',
                    'trn_store_orders.delivery_boy_id',
                    'trn_store_orders.coupon_id',
                    'trn_store_orders.coupon_code',
                    'trn_store_orders.reward_points_used',
                    'trn_store_orders.amount_before_applying_rp',
                    'trn_store_orders.trn_id',
                    'trn_store_orders.created_at',
                    'trn_store_orders.amount_reduced_by_coupon',
                    'trn_store_orders.order_type',
                    'trn_store_orders.delivery_date',
                    'trn_store_orders.delivery_time',

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
                    ->whereIn('status_id', [7, 8, 9]);


                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $deliveryReport = $deliveryReport->whereDate('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $deliveryReport = $deliveryReport->whereDate('trn_store_orders.created_at', '<=', $a2);
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


                $deliveryReport = $deliveryReport->where('trn_store_orders.store_id', $store_id)
                    ->orderBy('trn_store_orders.order_id', 'DESC');

                if (isset($request->page)) {
                    $deliveryReport = $deliveryReport->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $deliveryReport = $deliveryReport->paginate(10);
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


    public function listProducts(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {

                if ($data['productDetails']  = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                    // ->join('mst__taxes','mst_store_products.tax_id','=','mst__taxes.tax_id')
                    ->where('mst_store_products.store_id', $request->store_id)
                    ->where('mst_store_products.product_status', 1)
                    ->where('mst_store_product_varients.stock_count', '>', 0)
                    ->orderBy('mst_store_products.product_id', 'DESC')
                    ->select(
                        'mst_store_products.product_id',
                        'mst_store_products.product_name',
                        'mst_store_products.product_code',

                        'mst_store_product_varients.product_varient_id',
                        'mst_store_product_varients.variant_name',

                        'mst_store_product_varients.stock_count'
                    )->get()
                ) {
                    $data['status'] = 1;
                    $data['message'] = "success";
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                    return response($data);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Store not found ";
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
