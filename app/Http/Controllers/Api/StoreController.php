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
use App\Models\admin\Mst_store_product;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Mst_order_link_delivery_boy;
use App\Models\admin\Mst_StockDetail;
use App\Models\admin\Mst_store_link_delivery_boy;
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Trn_customer_enquiry;
use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Trn_OrderSplitPayments;
use App\Models\admin\Trn_store_order_item;
use App\Trn_store_referrals;
use File;
use App\User;
use Illuminate\Support\Facades\DB as FacadesDB;

class StoreController extends Controller
{

    public function storeVideoList(Request $request)
    {
        $data = array();
        try {

            $storeVids = Mst_Video::where('status', 1)->where('visibility', 1);
            if (isset($request->store_id)) {
                $storeTownData = Mst_store::find($request->store_id);
                $storeVids = $storeVids->where('town_id', $storeTownData->town_id)->orWhere('town_id',NULL);
            }else{
                
            }

            $storeVids = $storeVids->where('status', 1)->where('visibility', 1)->orderBy('video_id', 'DESC')->get();


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
    public function storeVideoListNew(Request $request)
{
    $data = array();
    try {
        $storeVids = Mst_Video::where('status', 1);

        if (isset($request->store_id)) {
            $storeTownData = Mst_store::find($request->store_id);
            $storeVids = $storeVids->where(function ($query) use ($storeTownData) {
                $query->where('town_id', $storeTownData->town_id)->orWhereNull('town_id');
            });
        } else {
            $storeVids = $storeVids->where(function ($query) {
                $query->whereNull('town_id');
            });
        }

        $storeVids = $storeVids->where('visibility', 1)->orderBy('video_id', 'DESC')->get();

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
            }

            if (!isset($v->video_discription)) {
                $v->video_discription = '';
            }

            if ($v->platform == 'Vimeo') {
                $revLink = strrev($v->video_code);
                $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                $linkCode = strrev($revLinkCode);
            }

            $v->link_code = @$linkCode;

            if ($v->video_image) {
                $v->video_image = '/assets/uploads/video_images/' . $v->video_image;
            } else {
                $v->video_image = Helper::default_video_image();
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
                $cusVids = $cusVids->where('town_id', $cusTownId)->orWhere('town_id','=',NULL);
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
    public function customerVideoListNew(Request $request)
{
    $data = array();
    try {
        $cusVids = Mst_Video::where('status', 1)->where('visibility', 2);

        if (isset($request->customer_id)) {
            $cusTownId = Trn_store_customer::find($request->customer_id)->town_id;
            $cusVids = $cusVids->where(function ($query) use ($cusTownId) {
                $query->where('town_id', $cusTownId)->orWhereNull('town_id');
            });
        } else {
            $cusVids = $cusVids->where(function ($query) {
                $query->whereNull('town_id');
            });
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
            }

            if (!isset($v->video_discription)) {
                $v->video_discription = '';
            }

            if ($v->platform == 'Vimeo') {
                $revLink = strrev($v->video_code);
                $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                $linkCode = strrev($revLinkCode);
            }

            $v->link_code = @$linkCode;

            if ($v->video_image) {
                $v->video_image = '/assets/uploads/video_images/' . $v->video_image;
            } else {
                $v->video_image = Helper::default_video_image();
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

    public function deliveryBoyVideoListNew(Request $request)
{
    $data = array();
    try {
        $dboyVid = Mst_Video::where('status', 1)->where('visibility', 3);

        if (isset($request->delivery_boy_id)) {
            $dbTownId = Mst_delivery_boy::find($request->delivery_boy_id)->town_id;
            $dboyVid = $dboyVid->where(function ($query) use ($dbTownId) {
                $query->where('town_id', $dbTownId)->orWhereNull('town_id');
            });
        } else {
            $dboyVid = $dboyVid->where(function ($query) {
                $query->whereNull('town_id');
            });
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
            }

            if (!isset($v->video_discription)) {
                $v->video_discription = '';
            }

            if ($v->platform == 'Vimeo') {
                $revLink = strrev($v->video_code);
                $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                $linkCode = strrev($revLinkCode);
            }

            $v->link_code = @$linkCode;

            if ($v->video_image) {
                $v->video_image = '/assets/uploads/video_images/' . $v->video_image;
            } else {
                $v->video_image = Helper::default_video_image();
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

    public function logoutTest(Request $request)
    {
        // echo "here";die;
        $accessToken = auth()->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        dd($accessToken, $token);
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
                   //commented to 777-Trn_StoreAdmin::where("store_mobile", '=', $storMob)->delete();
                    //Mst_store::where("store_mobile", '=', $storMob)->forceDelete();
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
                $store->store_contact_person_phone_number = $request->store_contact_person_phone_number;
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
                $store->gst            = $request->gst;
                $store->store_commision_percentage   = "2.00"; // default commision percentage - client update

                $timestamp = time();
                $qrco = Str::of($request->store_name)->slug('-') . "-" . rand(10, 99) . "-" . @$request->store_mobile;

                \QrCode::format('svg')->size(500)->generate($qrco, 'assets/uploads/store_qrcodes/' . $qrco . '.svg');
                $store->store_qrcode          = $qrco;
                $store->store_referral_id=uniqid();
                $store->subadmin_id          = 2; // default subadmin

                $store->latitude   = $request->latitude;
                $store->longitude   = $request->longitude;
                $store->place   = $request->place;
                $store->store_pincode   = $request->pincode;

                $store->save();

                $store_id = DB::getPdo()->lastInsertId();

                $insert['store_id'] = $store_id;
                $insert['admin_name'] = $request->store_name;
                //  $insert['email'] = $request->email;
                $insert['username'] = $request->store_mobile;
                $insert['store_mobile'] = $request->store_mobile;
                $insert['role_id'] = 0;
                $insert['store_account_status'] = 1;
                $insert['expiry_date'] = Carbon::now()->addDays(30)->toDateString();

                $insert['password'] = Hash::make($request->password);
                $insert['subadmin_id'] = 2;

                Trn_StoreAdmin::create($insert);

                // if(isset($request->store_gst_number))
                // {
                //  $store_doc->store_id            = $store_id;
                //  $store_doc->store_document_gstin            = null;
                //   $store_doc->save();
                //}




                $store_otp =  rand (100000,999999);
                $store_otp_expirytime = Carbon::now()->addMinute(10);

                $otp_verify->store_id                 = $store_id;
                $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                $otp_verify->store_otp                 = $store_otp;
                $otp_verify->save();

                $data['store_id'] = $store_id;
                $data['otp'] = $store_otp;
                $data['status'] = 1;
                $data['message'] = "Store Registration Success";
                $res=Helper::sendOtp($request->store_mobile,$store_otp,2);
                $data['otp_session_id']=$res['session_id'];
            } else {
                $data['errors'] = $validator->errors();
                $data['status'] = 2;
                $data['message'] = "Store already registered! Please login";
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
                    //here
                   
                    if (Hash::check($passChk, $custCheck->password)) {
                    
                    $parentStore =   Trn_StoreAdmin::where('store_id','=',$custCheck->store_id)->where('role_id',"=",0)->first();  
                    $st_check=Mst_store::where('store_id',$custCheck->store_id)->first();
                    if($st_check)
                    {
                        $data['store_referral_id']=$st_check->store_referral_id;
                    }
                    else
                    {
                        $data['store_referral_id']=0; 
                    }
                    if($today>$parentStore->expiry_date)
                    {
                        $sadmin = User::where('id','=', 1)->first();
                        if ($custCheck->role_id != 0)
                        {
                            $getStoreAdmin =   Trn_StoreAdmin::where('store_id','=',$custCheck->store_id)->where('role_id',"=",0)->first();
        
                            $phoneNumber = $getStoreAdmin->store_mobile;
                        }else{
                            $phoneNumber = $sadmin->phone_number;
                        }                        
                    $data['status'] = 8;
                    $data['message'] = "Profile not Activated/Profile Expired.Please contact Admin".$phoneNumber;
                    return response($data);
                    }
                  
                        // if (($custCheck->store_account_status != 0) || (($custCheck->store_account_status == 0) && ($today <= $custCheck->expiry_date))) {
                            if (($custCheck->store_account_status != 0) && ($today <= $custCheck->expiry_date)) {
                            if ($custCheck->store_otp_verify_status != 0) {
                                $data['status'] = 1;
                                $data['message'] = "Success";
                                $data['login_status '] = 1;
                                    $userIpAddress = $request->ip();
                                    $userType = 'store'; // You may need to customize this based on your application's logic
                                    $storeId = $custCheck->store_id; // You may need to set a specific store ID based on your application
                            
                                    DB::table('trn_user_logs')->insert([
                                        'user_ip_address' => $userIpAddress,
                                        'user_type' => $userType,
                                        'store_id' => $storeId,
                                        'store_admin_id' => $custCheck->store_admin_id,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);

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
                                    
                                    ->where('scopes','=','[]')
                                    ->where('revoked', 0)
                                    ->count();


                                $devTokenC = Trn_StoreDeviceToken::where('store_admin_id', $custCheck->store_admin_id)
                                    ->where('store_device_id', $request->device_id)
                                    ->count();
                                    //return $divTok;


                                if (($divTok > 0) && ($devTokenC == 0)) {
                                    $data['login_status '] =1 ; // logged in another device (otp)
                                    $store_otp=rand(100000,999999);
                                    $storeData = Mst_store::find($custCheck->store_id);
                                    //$otp_verify=Trn_store_otp_verify::where('store_id',$custCheck->store_id)->first();
                                    $store_otp_expirytime = Carbon::now()->addMinute(10);
                                    /*$otp_verify->store_id                 = $custCheck->store_id;
                                    $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                                    $otp_verify->store_otp                 = $store_otp;
                                    $otp_verify->update();*/
                                    //$res=Helper::sendOtp($phone,$store_otp,1);
                                    $data['otp_session_id']=1000;//$res['session_id'];
                                    $data['store_id'] = $custCheck->store_id;
                                    $data['store_admin_id'] = $custCheck->store_admin_id;
                                    $data['store_name'] = $storeData->store_name;
                                    $data['status'] = 1;
                                    $data['otp']=$store_otp;
                                    $data['message'] = "Success";
                                } else {
                                    $data['login_status '] = 0; // success 
                                }

                                //  $data['login_status '] = $divTok;
                            } else {
                                $store_otp=rand(100000,999999);
                                $storeData = Mst_store::find($custCheck->store_id);
                                $otp_verify=Trn_store_otp_verify::where('store_id',$custCheck->store_id)->first();
                                $store_otp_expirytime = Carbon::now()->addMinute(10);
                                $otp_verify->store_id                 = $custCheck->store_id;
                                $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                                $otp_verify->store_otp                 = $store_otp;
                                $otp_verify->update();
                                $res=Helper::sendOtp($phone,$store_otp,1);
                                $data['otp_session_id']=$res['session_id'];
                                $data['store_id'] = $custCheck->store_id;
                                $data['store_admin_id'] = $custCheck->store_admin_id;
                                $data['store_name'] = $storeData->store_name;
                                $data['status'] = 2;
                                $data['otp']=$store_otp;
                                $data['message'] = "OTP not verified";
                            }
                        } else {
                            //get phone number
                            $sadmin = User::where('id','=', 1)->first();
                                if ($custCheck->role_id != 0)
                                {
                                    $getStoreAdmin =   Trn_StoreAdmin::where('store_id','=',$custCheck->store_id)->where('role_id',"=",0)->first();
                                    $phoneNumber = $getStoreAdmin->store_mobile;
                                }else{
                                    $phoneNumber = $sadmin->phone_number;
                                }

                            if ($custCheck->store_account_status == 0) {
                                
                                
                                $data['status'] = 4;
                                $data['message'] = "Store is Inactive. Please contact Admin " .$phoneNumber;
                            } else {
                                $data['status'] = 4;
                                $data['message'] = "Profile not Activated. Please contact Admin ".$phoneNumber;
                            }
                        }
                    } else {
                        $data['status'] = 3;
                        $data['message'] = "Mobile Number or Password is Invalid";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Phone Number";
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
    public function sendStoreLoginOtp(Request $request)
    {
        $store_mobile=$request->store_mobile;
        $custCheck = Trn_StoreAdmin::where('store_mobile', '=',$store_mobile)->first();
        $today = Carbon::now()->toDateString();

        if ($custCheck) {  
            
           
            $parentStore =   Trn_StoreAdmin::where('store_id','=',$custCheck->store_id)->where('role_id',"=",0)->first();  
            //return $parentStore->expiry_date;
            if($today>$parentStore->expiry_date)
            {
                $sadmin = User::where('id','=', 1)->first();
                if ($custCheck->role_id != 0)
                {
                    $getStoreAdmin =   Trn_StoreAdmin::where('store_id','=',$custCheck->store_id)->where('role_id',"=",0)->first();

                    $phoneNumber = $getStoreAdmin->store_mobile;
                }else{
                    $phoneNumber = $sadmin->phone_number;
                }        
                               
            $data['status'] = 8;
            $data['message'] = "Profile not Activated/Profile Expired.Please contact Admin".$phoneNumber;
            return response($data);
            }
            
                
                //old
            // if (($custCheck->store_account_status != 0) || (($custCheck->store_account_status == 0) && ($today <= $custCheck->expiry_date))) {
            if (($custCheck->store_account_status != 0) && ($today <= $custCheck->expiry_date)) {
                
                    if ($custCheck->store_otp_verify_status != 0) {
                        $store_otp=rand(100000,999999);
                                $storeData = Mst_store::find($custCheck->store_id);
                                $otp_verify=Trn_store_otp_verify::where('store_id',$custCheck->store_id)->first();
                                $store_otp_expirytime = Carbon::now()->addMinute(10);
                                $otp_verify->store_id                 = $custCheck->store_id;
                                $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                                $otp_verify->store_otp                 = $store_otp;
                                $otp_verify->update();
                                $res=Helper::sendOtp($store_mobile,$store_otp,1);
                                $data['otp_session_id']=$res['session_id'];
                                $data['store_id'] = $custCheck->store_id;
                                $data['store_admin_id'] = $custCheck->store_admin_id;
                                $data['store_name'] = $storeData->store_name;
                                $data['status'] = 1;
                                $data['otp']=$store_otp;
                                $data['message'] = "OTP has been sent";
                                return response($data);
                    }
                }
            }
            $data['status']=0;
            $data['message']="store expired/in active";
            return response($data);
        

    }


    public function loginStore(Request $request)
    {
        $data = array();
        try {
            $phone = $request->input('store_mobile');
            $passChk = $request->input('password');
            // $devType = $request->input('device_type');
            // $devToken = $request->input('device_token');

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
                        $st_check=Mst_store::where('store_id',$custCheck->store_id)->first();
                        if($st_check)
                        {
                            $data['store_referral_id']=$st_check->store_referral_id;
                        }
                        else
                        {
                            $data['store_referral_id']=0; 
                        }
                    $parentStore =   Trn_StoreAdmin::where('store_id','=',$custCheck->store_id)->where('role_id',"=",0)->first();  
                    //return $parentStore->expiry_date;
                    if($today>$parentStore->expiry_date)
                    {   
                        $sadmin = User::where('id','=', 1)->first();
                        if ($custCheck->role_id != 0)
                        {
                            $getStoreAdmin =   Trn_StoreAdmin::where('store_id','=',$custCheck->store_id)->where('role_id',"=",0)->first();
        
                            $phoneNumber = $getStoreAdmin->store_mobile;
                        }else{
                            $phoneNumber = $sadmin->phone_number;
                        }             
                    $data['status'] = 8;
                    $data['message'] = "Profile not Activated/Profile Expired.Please contact Admin".$phoneNumber;
                    return response($data);
                    }
                    
                        
                        //old
                    // if (($custCheck->store_account_status != 0) || (($custCheck->store_account_status == 0) && ($today <= $custCheck->expiry_date))) {
                    if (($custCheck->store_account_status != 0) && ($today <= $custCheck->expiry_date)) {
                        
                            if ($custCheck->store_otp_verify_status != 0) {
                                
                                $data['status'] = 1;
                                $data['message'] = "Login Success";

                                
                                Trn_StoreDeviceToken::where('store_id', $custCheck->store_id)
                                    ->orwhere('store_device_id', $request->device_id)
                                    ->delete();
                                if (isset($request->device_token) && isset($request->device_type)) {

                                    $cdt = new Trn_StoreDeviceToken;
                                    $cdt->store_id = $custCheck->store_id;
                                    $cdt->store_admin_id = $custCheck->store_admin_id;
                                    $cdt->store_device_token = $request->device_token;
                                    $cdt->store_device_id = $request->device_id;
                                    $cdt->store_device_type = $request->device_type;
                                    $cdt->save();
                                }
                                


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
                                    $dataMobile =   Trn_StoreAdmin::find($custCheck->store_admin_id)->store_mobile;

                                if (($storeData->service_area != null) && ($storeData->service_area != 0)) {
                                    $data['serviceAreaData'] = 1;
                                } else {
                                    $data['serviceAreaData'] = 0;
                                }

                                $profileData = Helper::findStoreDataFilled($custCheck->store_id);
                                $data['profileData'] = $profileData;
                                $data['store_name'] = $storeData->store_name;
                                $data['store_admin_name'] = $dataName;
                                $data['store_username'] = $storeData->store_username;
                                $data['store_mobile_number'] = $dataMobile;
                                $data['access_token'] = $custCheck->createToken('authToken')->accessToken;

                                $divTok = DB::table('oauth_access_tokens')
                                    ->where('user_id', $custCheck->store_admin_id)
                                    ->where('scopes', [])
                                    ->where('revoked', 0)
                                    ->count();
                                $divTok = $divTok - 1;
                                if ($divTok > 0) {
                                    $data['login_status '] = 1;
                                    $userIpAddress = $request->ip();
                                    $userType = 'store'; // You may need to customize this based on your application's logic
                                    $storeId = $custCheck->store_id; // You may need to set a specific store ID based on your application
                            
                                    DB::table('trn_user_logs')->insert([
                                        'user_ip_address' => $userIpAddress,
                                        'user_type' => $userType,
                                        'store_id' => $storeId,
                                        'store_admin_id' => $custCheck->store_admin_id,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                    
                                } else {
                                    $data['login_status '] = 0;
                                }

                                $data['onBoardingStatus'] = Helper::onBoardingStatus($custCheck->store_id);


                                // $data['isProfileFilled'] = Helper::isProfileFilled($custCheck->store_id);
                                // $data['isServiceAreaSet'] = Helper::isServiceAreaSet($custCheck->store_id);
                                // $data['isWorkingDaysSet'] = Helper::isWorkingDaysSet($custCheck->store_id);
                                

                            } else {
                                
                                $store_otp=rand(100000,999999);
                                $storeData = Mst_store::find($custCheck->store_id);
                                $otp_verify=Trn_store_otp_verify::where('store_id',$custCheck->store_id)->first();
                                $store_otp_expirytime = Carbon::now()->addMinute(10);
                                $otp_verify->store_id                 = $custCheck->store_id;
                                $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                                $otp_verify->store_otp                 = $store_otp;
                                $otp_verify->update();
                                $res=Helper::sendOtp($phone,$store_otp,1);
                                $data['otp_session_id']=$res['session_id'];
                                $data['store_id'] = $custCheck->store_id;
                                $data['store_admin_id'] = $custCheck->store_admin_id;
                                $data['store_name'] = $storeData->store_name;
                                $data['status'] = 2;
                                $data['otp']=$store_otp;
                                $data['message'] = "OTP not verified";
                            }
                        } else {
                            //get phone number
                            $sadmin = User::where('id','=', 1)->first();
                                if ($custCheck->role_id != 0)
                                {
                                    $getStoreAdmin =   Trn_StoreAdmin::where('store_id','=',$custCheck->store_id)->where('role_id',"=",0)->first();
                
                                    $phoneNumber = $getStoreAdmin->store_mobile;
                                }else{
                                    $phoneNumber = $sadmin->phone_number;
                                }

                            if ($custCheck->store_account_status == 0) {
                                $data['status'] = 4;
                                $data['message'] = "Store is inactive. Please contact Admin ".$phoneNumber;
                            } else {
                                $data['status'] = 4;
                                $data['message'] = "Profile not Activated/Profile Expired.Please contact Admin ".$phoneNumber;
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
                //$otp = $request->otp_status;
                $otp = $request->store_otp; //old
                //if ($otp == 'accepted') {
                $session_id=$request->otp_session_id;
               $res=Helper::verifyOtp($session_id,$otp,1);
               if($res['status']=="success")
               {
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
            $response = ['status' => '0', 'message' => 'Invalid OTP...Try Again'];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => 'Invalid OTP...Try Again'];
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
                    $store_otp = rand(100000,999999);
                    $storeData = Mst_store::find($store_id);
                    $store_otp_verify_id = $otp_verify->store_otp_verify_id;
                    $otp_verify = Trn_store_otp_verify::Find($store_otp_verify_id);
                    $extented_time = Carbon::now()->addMinute(10);
                    $otp_verify->store_otp_expirytime = $extented_time;
                    $otp_verify->store_otp=$store_otp;
                    $otp_verify->update();
                    if($request->store_mobile)
                    {
                        $res=Helper::sendOtp($request->store_mobile,$store_otp,1);

                    }
                    else
                    {
                        $res=Helper::sendOtp($storeData->store_mobile,$store_otp,1);

                    }
                    
                    $data['otp_session_id']=$res['session_id'];
                    $data['status'] = 1;
                    $data['otp'] = $store_otp;
                    $data['message'] = "OTP resent Success.";
                }/* else {
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
                }*/
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
                    $store_otp =  rand (100000,999999);
                    //$store_otp =  5555;
                    $store_otp_expirytime = Carbon::now()->addMinute(10);

                    $otp_verify->store_id                 = $store_id;
                    $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                    $otp_verify->store_otp                = $store_otp;
                    $otp_verify->save();

                    $data['status'] = 1;
                    $data['store_id'] = $store_id;
                    $data['store_mobile'] = $store_mob;
                    $data['store_otp'] = $store_otp;
                    $res=Helper::sendOtp($mobNumber,$store_otp,1);
                    $data['otp_session_id']=$res['session_id'];
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
            $otp_session_id=$request->otp_session_id;

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

                        $res=Helper::verifyOtp($otp_session_id,$otp,1);
                        if($res['status']=="success")
                        {
                         $data['status'] = 1;
                         $data['store_id'] = $store_id;
                         $data['store_mobile'] = $store_mob;
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

                    $data['onBoardingStatus'] = Helper::onBoardingStatus($request->store_id);


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
                if ($custCheck->store_account_status != 0) {
                    $data['active_status'] = 1;
                } else {
                    $data['active_status'] = 0;
                }




                $data['online_status'] = $statusStore->online_status;
                $data['status'] = 1;
                $products=Helper::minimumStockProducts($request->store_id);
                $minStockProducts=$products->map(function($data){
                    $var=Mst_store_product_varient::where('product_varient_id',$data['product_varient_id'])->first();
        
                    $product['product_name']=$data['variant_name'];
                    $product['prodcut_id']=$data['product_id'];
                    $product['product_varient_id']=$data['product_varient_id'];
                    $product['stock_count']=$var['stock_count'];
                    $product['minimum_stock']=$data['stock_count']??0;
                    return $product;
            });
                $data['minimumStockProducts']=$minStockProducts;
                $newOrders=Trn_store_order::whereDate('created_at', Carbon::today())->where('store_id',$request->store_id)->where('status_id',1)->whereNull('TEST')->latest()->limit(4)->get()->map(function($data){
                    //$subdata=json_decode($data->data);
                    $qry['order_id']= $data->order_id;
                    $qry['order_number']=$data->order_number;
                    $qry['TEST']= 0;
                    $qry['total']= (float)$data->product_total_amount;
                    $qry['updated_at']= $data->updated_at->diffForHumans();
                    return $qry;
                  });
                  $data['newOrders']=$newOrders;

            /*$newEnquiries=Trn_customer_enquiry::whereDate('created_at', Carbon::today())->where('store_id',$request->store_id)->latest()->limit(4)->get()->map(function($data){
                //$subdata=json_decode($data->data);
                $enq['order_id']= $data->order_id;
                $qry['order_number']=$data->order_number;
                $qry['TEST']= 0;
                $qry['total']= (float)$data->product_total_amount;
                $qry['updated_at']= $data->updated_at->diffForHumans();
                return $qry;
              });*/

            } else {
                $data['status'] = 0;
                $data['message'] = "Store does not exist";
            }
            return response($data);
        } catch (\Exception $e) {
            //$response = ['status' => '0', 'message' => $e->getMessage()];
            $data['status']=0;
            $data['message']="User already logged in Another device";
            $data['login_status']=0;
            return response($data);
        } catch (\Throwable $e) {
            //$response = ['status' => '0', 'message' => $e->getMessage()];
            $data['status']=0;
            $data['message']="User already logged in Another device";
            
            $data['login_status']=0;
            return response($data);
        }
    }

    public function getLoginOnlineStatus2(Request $request)
    {
        // echo "here";
        // die;
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                //dd(auth()->user()->token());
                if ($accessToken = auth()->user()->token()) {
                    dd($accessToken);
                    $token =  $request->user()->tokens->find($accessToken);

                    $divTok = DB::table('oauth_access_tokens')
                        ->where('id', $token->id)
                        ->first();

                    $data['onBoardingStatus'] = Helper::onBoardingStatus($request->store_id);


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
                if ($custCheck->store_account_status != 0) {
                    $data['active_status'] = 1;
                } else {
                    $data['active_status'] = 0;
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
                $products=Helper::minimumStockProducts($request->store_id);
                $minStockProducts=$products->map(function($data){
                    $var=Mst_store_product_varient::where('product_varient_id',$data['product_varient_id'])->first();
        
                    $product['product_name']=$data['variant_name'];
                    $product['prodcut_id']=$data['product_id'];
                    $product['product_varient_id']=$data['product_varient_id'];
                    $product['stock_count']=$var['stock_count'];
                    $product['minimum_stock']=$data['stock_count']??0;
                    return $product;
            });
                $data['minimumStockProducts']=$minStockProducts;
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
                    'trn_store_orders.reward_points_used_store',
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

                if (isset($request->customer_mobile_number)) {


                    $salesData = $salesData->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
                 
              }

                // if (isset($request->customer_id)) {
                //     $salesData = $salesData->where('trn_store_orders.customer_id', '=', $request->customer_id);
                // }

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
                    'trn_store_orders.reward_points_used_store',
                    
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


                if (isset($request->customer_mobile_number)) {
                    $salesData = $salesData->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
                 
              }

                // if (isset($request->customer_id)) {
                //     $salesData = $salesData->where('trn_store_orders.customer_id', '=', $request->customer_id);
                // }

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

                    //'trn_store_customers.customer_id',
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
                if (isset($request->customer_mobile_number)) {
                    $salesData = $salesData->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
                 
                }

                // if (isset($request->customer_id)) {
                //     $salesData = $salesData->where('trn_store_orders.customer_id', '=', $request->customer_id);
                // }

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

                    $cusData = Trn_store_customer::find($sd->customer_id);
                    $sd->cusData = $cusData;
                    if (!isset($cusData->customer_first_name))
                        $sd->customer_first_name = '';

                    if (!isset($cusData->customer_last_name))
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

    public function overallProductReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;

                $inventoryData =   Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
              
                ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
                ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
          
                ->where('mst_store_products.store_id', $store_id)
                
          
                ->where('mst_store_products.product_type', 1)
                // ->where('mst_store_products.is_removed',0)
                // ->orderBy('mst_store_products.product_name','ASC')
                //   ->orderBy('mst_store_product_varients.stock_count', 'ASC')
          
          
                ->select(
                  'mst_store_products.product_id',
                  'mst_store_products.product_name',
                  'mst_store_products.product_code',
                  'mst_store_products.product_cat_id',
                  'mst_store_products.product_base_image',
                  'mst_store_products.product_status',
                  'mst_store_products.product_brand',
                  'mst_store_products.min_stock',
                  'mst_store_products.is_removed',
          
                  'mst_store_products.tax_id',
                  'mst_store_product_varients.product_varient_id',
                  'mst_store_product_varients.variant_name',
                  'mst_store_product_varients.product_varient_price',
                  'mst_store_product_varients.product_varient_offer_price',
                  'mst_store_product_varients.product_varient_base_image',
                  'mst_store_product_varients.stock_count',
                  'mst_store_product_varients.created_at',
                  'mst_store_product_varients.is_base_variant',
                  'mst_store_product_varients.variant_status',
                  'mst_store_categories.category_id',
                  'mst_store_categories.category_name',
                  'mst__sub_categories.sub_category_name',
          
                );
                // $inventoryData = $inventoryData->get();
                // dd($inventoryData);



                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if(isset($request->date_from))
                 {
                  $inventoryData = $inventoryData->whereDate('mst_store_product_varients.created_at','>=',$a1);
                 }

                 if(isset($request->date_to))
                 {
                   $inventoryData = $inventoryData->whereDate('mst_store_product_varients.created_at','<=',$a2);
                 }

                if (isset($request->product_id)) {
                    $inventoryData = $inventoryData->where('mst_store_products.product_id', $request->product_id);
                }

                if (isset($request->type_id)) {
                    if($request->type_id==2)
                    {
                      $type_id=0;
                    }
                    else{
                      $type_id=1;
                    }
                    $inventoryData = $inventoryData->where('mst_store_product_varients.is_base_variant',$type_id);
                  }

                if (isset($request->category_id)) {
                    $inventoryData = $inventoryData->where('mst_store_categories.category_id', $request->category_id);
                }

                if (isset($request->sub_category_id)) {
                    $inventoryData = $inventoryData->where('mst__sub_categories.sub_category_id', $request->sub_category_id);
                }



                $inventoryData = $inventoryData->orderBy('mst_store_product_varients.created_at', 'DESC');
                $roWc = count($inventoryData->get());
                //return $roWc;

                $inventoryDataa = $inventoryData->skip(($request->page - 1) * 20)->take(20)->get();
                
                //$roWc = 0;
                if ($roWc == 0) {
                    $inventoryData22 =   Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                        ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                      
                        ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
                        ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
                        ->where('mst_store_products.store_id', $store_id)
                       
                        ->where('mst_store_products.product_type', 1)
                        ->orderBy('mst_store_product_varients.product_varient_id', 'DESC');
                       
                      

                    if (isset($request->product_id)) {
                        $inventoryData22 = $inventoryData22->where('mst_store_products.product_id', $request->product_id);
                    }

                    if (isset($request->type_id)) {
                        if($request->type_id==2)
                        {
                          $type_id=0;
                        }
                        else{
                          $type_id=1;
                        }
                        $inventoryData = $inventoryData->where('mst_store_product_varients.is_base_variant',$type_id);
                      }

                    if (isset($request->category_id)) {
                        $inventoryData22 = $inventoryData22->where('mst_store_categories.category_id', $request->category_id);
                    }

                    if (isset($request->sub_category_id)) {
                        $inventoryData22 = $inventoryData22->where('mst__sub_categories.sub_category_id', $request->sub_category_id);
                    }
                    $roWz = $inventoryData22->get();
                    $roWc = count($roWz);
                }




                $inventoryDatasss = collect($inventoryDataa);
                
                $dataReViStoreSS =   $inventoryDatasss->values()->all();



                $data['inventoryData'] = $dataReViStoreSS;
                //return count($data['inventoryData']);
                //return $roWc;
                if ($roWc > 19) {
                    $data['pageCount'] = ceil(@$roWc / 20);
                    //return $data['pageCount'];

                } else {
                    $data['pageCount'] = 1;
                }
                //return $data['pageCount'];
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

                $inventoryData =   Mst_store_product_varient::leftjoin('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                    ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                    ->leftjoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
                    ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
                    ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')

                    ->where('mst_store_products.store_id', $store_id)
                    ->where('mst__stock_details.stock', '>', 0)

                    ->where('mst_store_products.product_type', 1)
                    // ->where('mst_store_products.is_removed', 0)
                     ->where('mst_store_product_varients.is_removed', 0)
                    // ->orderBy('mst_store_products.product_name','ASC')
                    //  ->orderBy('mst_store_product_varients.stock_count', 'ASC')

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
                        'mst_store_product_varients.is_base_variant',
                        'mst_store_product_varients.variant_status',
                        'mst_store_categories.category_id',
                        'mst_store_categories.category_name',
                        'mst__stock_details.stock',
                        'mst__stock_details.prev_stock',
                        'mst__stock_details.created_at AS updated_time',
                        'mst_store_agencies.agency_name',
                        'mst__sub_categories.sub_category_name',

                    );
                // $inventoryData = $inventoryData->get();
                // dd($inventoryData);



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
               



                $inventoryData = $inventoryData->orderBy('updated_time', 'DESC')->get();


                // $inventoryDataa = $inventoryData->skip(($request->page - 1) * 20)->take(20)->get();

                // $roWc = 0;
                // if ($roWc == 0) {
                //     $inventoryData22 =  Mst_store_product_varient::leftjoin('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                //     ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                //     ->leftjoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
                //     ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
                //     ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')

                //     ->where('mst_store_products.store_id', $store_id)
                //     ->where('mst__stock_details.stock', '>', 0)

                //     ->where('mst_store_products.product_type', 1)
                //      ->where('mst_store_products.is_removed', 0)
                //     ->where('mst_store_product_varients.is_removed', 0)
                //     // ->orderBy('mst_store_products.product_name','ASC')
                //     //  ->orderBy('mst_store_product_varients.stock_count', 'ASC')

                //     ->select(
                //         'mst_store_products.product_id',
                //         'mst_store_products.product_name',
                //         'mst_store_products.product_code',
                //         'mst_store_products.product_cat_id',
                //         'mst_store_products.product_base_image',
                //         'mst_store_products.product_status',
                //         'mst_store_products.product_brand',
                //         'mst_store_products.min_stock',

                //         'mst_store_products.tax_id',
                //         'mst_store_product_varients.product_varient_id',
                //         'mst_store_product_varients.variant_name',
                //         'mst_store_product_varients.product_varient_price',
                //         'mst_store_product_varients.product_varient_offer_price',
                //         'mst_store_product_varients.product_varient_base_image',
                //         'mst_store_product_varients.stock_count',
                //         'mst_store_product_varients.created_at',
                //         'mst_store_product_varients.is_base_variant',
                //         'mst_store_product_varients.variant_status',
                //         'mst_store_categories.category_id',
                //         'mst_store_categories.category_name',
                //         'mst__stock_details.stock',
                //         'mst__stock_details.prev_stock',
                //         'mst__stock_details.created_at AS updated_time',
                //         'mst_store_agencies.agency_name',
                //         'mst__sub_categories.sub_category_name',

                //     );

                //     if (isset($request->product_id)) {
                //         $inventoryData22 = $inventoryData22->where('mst_store_products.product_id', $request->product_id);
                //     }

                //     if (isset($request->agency_id)) {
                //         $inventoryData22 = $inventoryData22->where('mst_store_agencies.agency_id', $request->agency_id);
                //     }

                //     if (isset($request->category_id)) {
                //         $inventoryData22 = $inventoryData22->where('mst_store_categories.category_id', $request->category_id);
                //     }

                //     if (isset($request->sub_category_id)) {
                //         $inventoryData22 = $inventoryData22->where('mst__sub_categories.sub_category_id', $request->sub_category_id);
                //     }
                //     $roWz = $inventoryData22->get();
                //     $roWc = count($roWz);
                // }


                foreach($inventoryData as $da)
                {
                    // if($da->stock_count==0)
                    // {
                    //     if($da->prev_stock>0)
                    //     {
                    //         $da->prev_stock=$da->prev_stock+$da->stock;
                    //         $da->stock=0-$da->prev_stock;
                    //         $da->prev_stock=(string)$da->prev_stock;
                    //         $da->stock=(string)$da->stock;
                    //     }
                       
                    // }
                    // if($da->stock>0&&$da->prev_stock==0)
                    // {
                    //     $st=$da->stock;
                    //     $da->stock=$da->stock_count-$da->stock;
                    //     $da->prev_stock=(string)$st;
                    //     $da->stock=(string)$da->stock;
                    // }
                   $stock_info= Mst_StockDetail::where('product_varient_id',$da->product_varient_id)->orderBy('stock_detail_id','DESC')->first();
                   if($stock_info)
                   {
                    $da->prev_stock=$stock_info->prev_stock;
                    $da->stock=$stock_info->stock;
                    $da->prev_stock=(string)$da->prev_stock;
                    $da->stock=(string)$da->stock;
                    $da->updated_time = Carbon::parse($stock_info->created_at)->format('Y-m-d H:i:s');

                   }

                }
                $inventoryData = $inventoryData->sortByDesc(function ($item) {
                    return $item->updated_time;
                });
                
                // If you want to maintain the original keys after sorting, you can use values() to reset the keys:
                $inventoryData = $inventoryData->values();
                $inventoryDatasss = collect($inventoryData);
                $inventoryDatassss=$inventoryDatasss->unique('product_varient_id');
                $perPage = 15;
                $page=$request->page??1;
                $offset = ($page - 1) * $perPage;
                $roWc=count($inventoryDatassss);
                $dataReViStoreSS =   $inventoryDatassss->slice($offset, $perPage)->values()->all();



                $data['inventoryData'] = $dataReViStoreSS;
                if ($roWc >14) {
                    $data['pageCount'] = floor(@$roWc /15);
                 } else {
                     $data['pageCount'] = 1;
                 }
                $data['status'] = 1;
                $data['currentPage']=$page;
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
                    ->leftjoin('empty_stock_log', 'empty_stock_log.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
                    ->where('mst_store_products.store_id', $store_id)
                    ->where('mst_store_product_varients.stock_count', '<=', 0)
                    ->where('mst_store_products.product_type', 1)
                    // ->orderBy('mst_store_products.product_name','ASC')
                    
                    ->where('mst_store_products.is_removed', 0)
                    ->where('mst_store_product_varients.is_removed', 0)
                    ->whereNotNull('empty_stock_log.created_time')
                    ->orderBy('empty_stock_log.created_time', 'DESC')

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
                        'mst_store_product_varients.is_base_variant',
                        'mst_store_product_varients.variant_status',
                        //'mst__stock_details.created_at AS updated_time',
                        //'mst__stock_details.created_at',
                        'mst_store_categories.category_id',
                        'mst_store_categories.category_name',
                        'mst__stock_details.stock',
                        'mst__stock_details.prev_stock',
                        'mst_store_agencies.agency_name',
                        'mst__sub_categories.sub_category_name',
                        'empty_stock_log.created_time  as updated_time'

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

                $inventoryData = $inventoryData->groupBy('mst_store_product_varients.product_varient_id');

                if (isset($request->page)) {
                    $inventoryData = $inventoryData->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $inventoryData = $inventoryData->paginate(10);
                }


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

                if (isset($request->customer_mobile_number)) {
                    $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
                 
                }


                // if (isset($request->customer_id)) {
                //     $paymentReport = $paymentReport->where('trn_store_orders.customer_id', '=', $request->customer_id);
                // }

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
   
                    // if (($sd->payment_type_id == 2) || $sd->status_id == 9)
                    //     $sd->payment_status = 'Success';
                    // else
                    //     $sd->payment_status = 'Pending';
                    if($sd->status_id != 5 )
                    {
                        if($sd->payment_type_id == 1 && $sd->status_id == 9) 
                        {
                            $sd->payment_status = 'Success';
                        }elseif($sd->payment_type_id == 2  || $sd->payment_type_id == 0){
                            $sd->payment_status = 'Success';
                        }else{
                            $sd->payment_status = 'Pending';
                        }
                    }else{
                    $sd->payment_status = 'Pending';
                    }



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

                if (isset($request->customer_mobile_number)) {
                    $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
                 
              }


                // if (isset($request->customer_id)) {
                //     $paymentReport = $paymentReport->where('trn_store_orders.customer_id', '=', $request->customer_id);
                // }

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
                        $sd->payment_status = 'Pending';



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
                            } else {
                                $row->orderSplitPayments = [];
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
                    // ->whereIn('status_id', [7, 8, 9]);
                    ->where('status_id', 9);


                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $deliveryReport = $deliveryReport->whereDate('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $deliveryReport = $deliveryReport->whereDate('trn_store_orders.created_at', '<=', $a2);
                }

                if (isset($request->customer_mobile_number)) {
                    $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
                 
              }


                // if (isset($request->customer_id)) {
                //     $deliveryReport = $deliveryReport->where('trn_store_orders.customer_id', '=', $request->customer_id);
                // }

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
    public function deliveryBoyPayoutReport(Request $request)
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
                    'mst_delivery_boys.delivery_boy_mobile',
                    'mst_delivery_boys.delivery_boy_commision',
                    'mst_delivery_boys.delivery_boy_commision_amount'



                )
                    ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
                    ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
                    ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id')
                    // ->whereIn('status_id', [7, 8, 9]);
                    ->where('status_id', 9);


                $a1 = Carbon::parse($request->date_from)->startOfDay();
                $a2  = Carbon::parse($request->date_to)->endOfDay();

                if (isset($request->date_from)) {
                    $deliveryReport = $deliveryReport->whereDate('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $deliveryReport = $deliveryReport->whereDate('trn_store_orders.created_at', '<=', $a2);
                }

                if (isset($request->customer_mobile_number)) {
                    $data = $deliveryReport->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
                 
              }


                // if (isset($request->customer_id)) {
                //     $deliveryReport = $deliveryReport->where('trn_store_orders.customer_id', '=', $request->customer_id);
                // }

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
                    ->where('trn_store_orders.delivery_status_id', '=', 3)
                    ->whereNotNull('mst_delivery_boys.delivery_boy_name')
                    ->orderBy('trn_store_orders.order_id', 'DESC');

                if (isset($request->page)) {
                    $deliveryReport = $deliveryReport->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $deliveryReport = $deliveryReport->paginate(10);
                }
                $check_array=[];
        $i = 0;
        $tot_pre=[];
        $tot_now=[];
        $tot_prev_count=[];
        $tot_now_count=[];
        $tot_prev_count[0]=0;
        $tot_now_count[0]=0;
        $prev_amount[0]=0;
        $month_commision[0]=0;
        $prev_amount = []; // Use an associative array to store previous amounts for each delivery boy
        
        



                foreach ($deliveryReport->reverse() as $sd) {
                   /* $i++;
          
                    array_push($check_array,$sd->order_id);
                    $orlink=Mst_order_link_delivery_boy::where('order_id',$sd->order_id)->where('delivery_boy_id',@$sd->delivery_boy_id)->first();
                    $total_count=Trn_store_order::whereIn('order_id',$check_array)->where('delivery_boy_id',@$sd->delivery_boy_id)->orderBy('order_id','DESC')->count();
                    $tot_now_count[$i]=$total_count;
                    $tot_prev_count[$i]=$tot_now_count[$i]-1;
                    $sd->orderTotalDiscount = Helper::orderTotalDiscount($sd->order_id);
                    $sd->orderTotalTax = Helper::orderTotalTax($sd->order_id);
                    $sd->subadmin_name=Helper::subAdminName($sd->subadmin_id)??'';
                    $sd->subadmin_phone=$sd->subadmindetail->phone??'';
                    $month_commision[$i]=$orlink->commision_per_month;
                    $sd->commission_month=$orlink->commision_per_month??$sd->delivery_boy_commision;
                    $sd->commission_order=$orlink->commision_per_order??$sd->delivery_boy_commision_amount;
                    // $prev_amount_numeric = (float)number_format($prev_amount[$i-1]);
                    // $commission_order_numeric = number_format((float)$sd->commission_order);
                    $prev_amount_numeric = is_numeric($prev_amount[$i-1]) ? (float) $prev_amount[$i-1] : 0;
$commission_order_numeric = is_numeric($sd->commission_order) ? (float) $sd->commission_order : 0;
                    $sd->previous_commission=number_format((float)$prev_amount[$i-1]);
                    $sd->commission_after_order= $prev_amount_numeric+$commission_order_numeric;
                    $prev_amount[$i]=$sd->commission_after_order;
                    //$sd->previous_commission=$sd->previous_commission+$sd->commission_month;
                    $previous_commission_numeric = is_numeric($sd->previous_commission) ? (float) $sd->previous_commission : 0;
                    $commission_month_numeric = is_numeric($sd->commission_month) ? (float) $sd->commission_month : 0;
                    $sd->previous_commission = $previous_commission_numeric + $commission_month_numeric;

                    $sd->commission_after_order=$sd->commission_after_order+$sd->commission_month;*/
                    $i++;
                    array_push($check_array, $sd->order_id);
                    $orlink = Mst_order_link_delivery_boy::where('order_id', $sd->order_id)->where('delivery_boy_id', @$sd->delivery_boy_id)->first();
                    $total_count = Trn_store_order::whereIn('order_id', $check_array)->where('delivery_boy_id', @$sd->delivery_boy_id)->orderBy('order_id', 'DESC')->count();
                    $tot_now_count[$i] = $total_count;
                    $tot_prev_count[$i] = $tot_now_count[$i] - 1;
                    $sd->orderTotalDiscount = Helper::orderTotalDiscount($sd->order_id);
                    $sd->orderTotalTax = Helper::orderTotalTax($sd->order_id);
                    $sd->subadmin_name = Helper::subAdminName($sd->subadmin_id) ?? '';
                    $sd->subadmin_phone = $sd->subadmindetail->phone ?? '';
                    $month_commision[$i] = $orlink->commision_per_month;
                    $sd->commission_month = $orlink->commision_per_month ?? $sd->delivery_boy_commision;
                    $sd->commission_order = $orlink->commision_per_order ?? $sd->delivery_boy_commision_amount;
    
                    $delivery_boy_id = $sd->delivery_boy_id;
    
                    if (!isset($prev_amount[$delivery_boy_id])) {
                        $prev_amount[$delivery_boy_id] = 0;
                    }
    
                    $prev_amount_numeric = is_numeric($prev_amount[$delivery_boy_id]) ? (float) $prev_amount[$delivery_boy_id] : 0;
                    $commission_order_numeric = is_numeric($sd->commission_order) ? (float) $sd->commission_order : 0;
    
                    $sd->previous_commission = number_format($prev_amount_numeric);
                    $sd->commission_after_order = $prev_amount_numeric + $commission_order_numeric;
                    $prev_amount[$delivery_boy_id] = $sd->commission_after_order;
    
                    $previous_commission_numeric = is_numeric($sd->previous_commission) ? (float) $sd->previous_commission : 0;
                    $commission_month_numeric = is_numeric($sd->commission_month) ? (float) $sd->commission_month : 0;
    
                    $sd->previous_commission = $previous_commission_numeric + $commission_month_numeric;
                    $sd->commission_after_order = $sd->commission_after_order + $sd->commission_month;

                    //////////////////////////////////////////////
        //             $cm=$orlink->commision_per_month;
        // $co=$orlink->commision_per_order;
        // $d->previous_amount=$prev_amount[$i-1];
        // $d->new_amount=$prev_amount[$i-1]+@$co;
        // $prev_amount[$i]=$d->new_amount;
        // $d->c_month= $cm;
        // $d->c_order=$co;


                    //////////////////////////////////////////////
                    
                   

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


                $data['deliveryboyPayoutReport'] = $deliveryReport;
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

    public function refundReport(Request $request)
    {
        try {

            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $salesData = Trn_store_order::where('isRefunded','=',1)->orWhere('isRefunded','=',2)->where('trn_store_orders.store_id','=',$request->store_id)->select(

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
                    'trn_store_orders.referenceId',
                    'trn_store_orders.created_at',
                    'trn_store_orders.amount_reduced_by_coupon',
                    'trn_store_orders.order_type',
                    'trn_store_orders.isRefunded',
                    'trn_store_orders.refundId',
                    //'trn_store_orders.refundStatus',
                    'trn_store_orders.refundNote',
                    'trn_store_orders.refundProcessStatus',
                    // 'trn_store_orders.refundProcessDate',
                    // 'trn_store_orders.refundStartDate',

                    'trn_store_customers.customer_id',
                    'trn_store_customers.customer_first_name',
                    'trn_store_customers.customer_last_name',
                    'trn_store_customers.customer_mobile_number',
                    'trn_store_customers.place',

                    //'mst_stores.store_id',
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
                    $salesData = $salesData->where('trn_store_orders.created_at', '>=', $a1);
                }

                if (isset($request->date_to)) {
                    $salesData = $salesData->where('trn_store_orders.created_at', '<=', $a2);
                }

                if (isset($request->customer_mobile_number)) {
                    $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
                 
              }

                // if (isset($request->customer_id)) {
                //     $salesData = $salesData->where('trn_store_orders.customer_id', '=', $request->customer_id);
                // }

                if (isset($request->delivery_boy_id)) {
                    $salesData = $salesData->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
                }

                if (isset($request->status_id)) {
                    $salesData = $salesData->where('trn_store_orders.status_id', '=', $request->status_id);
                }

                if (isset($request->order_type)) {
                    $salesData = $salesData->where('trn_store_orders.order_type', '=', $request->order_type);
                }


                $salesData = $salesData
                    ->whereIn('isRefunded', ['1', '2'])
                    ->where('trn_store_orders.store_id','=',$request->store_id)
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
                        
                    if ($sd->isRefunded == 1)
                        $sd->refund_status =  'Pending'; // to be changed to inprogress
                    elseif ($sd->isRefunded == 2)
                        $sd->refund_status =  'Success';
                    else
                        $sd->refund_status =  ''; 

                    @$sd->status->status;

                   
                }

                $data['refundData'] = $salesData;
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
    {  //will show all products regradless of stock and product status
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                //old

                // if ($data['productDetails']  = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                //     // ->join('mst__taxes','mst_store_products.tax_id','=','mst__taxes.tax_id')
                //     ->where('mst_store_products.store_id', $request->store_id)
                //     //->where('mst_store_products.product_status', 1)
                //     //->where('mst_store_product_varients.stock_count', '>', 0)
                //     ->orderBy('mst_store_products.product_id', 'DESC')
                //     ->select(
                //         'mst_store_products.product_id',
                //         'mst_store_products.product_name',
                //         'mst_store_products.product_code',

                //         'mst_store_product_varients.product_varient_id',
                //         'mst_store_product_varients.variant_name',

                //         'mst_store_product_varients.stock_count'
                //     )->get()
                // ) {

                    //new

                    if ($data['productDetails']  = Mst_store_product::join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                    ->select('mst_store_products.product_id', 'mst_store_products.product_name')
                    ->where('mst_store_products.store_id', $request->store_id)->orderBy('mst_store_products.product_id', 'DESC')->get())
                    
                    {
                
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

    
    public function inventoryListProducts(Request $request)
    { //will show products with stock >0  and product status = active and inactive
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {

                if ($data['productDetails']  = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                    // ->join('mst__taxes','mst_store_products.tax_id','=','mst__taxes.tax_id')
                    ->where('mst_store_products.store_id', $request->store_id)
                    //->where('mst_store_products.product_status', 1)
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

    
    public function outOfStockListProducts(Request $request)
    { //will show out of stock products stock = 0  and product status = active and inactive
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {

                if ($data['productDetails']  = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                    // ->join('mst__taxes','mst_store_products.tax_id','=','mst__taxes.tax_id')
                    ->where('mst_store_products.store_id', $request->store_id)
                    //->where('mst_store_products.product_status', 1)
                    ->where('mst_store_product_varients.stock_count', '<=', 0)
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
    public function walletReport(Request $request)
	{
	
			
			$store_id=$request->store_id;
            //dd($store_id);
            $walletdata=array();
            $resData=array();
			
			$datefrom = '';
			$dateto = '';
	  
	  
			
	  
			// $subadmins = User::where('user_role_id', '!=', 0)->get();
	  
			// $customers = Trn_store_customer::all();
	  
			// $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
			//   ->get();
	  
			// $orderStatus = Sys_store_order_status::all();
	  
	  
	  
			$data = Trn_store_order::select(
	  
			  'trn_store_orders.order_id',
              'trn_store_orders.store_id',
			  'trn_store_orders.order_number',
			  'trn_store_orders.customer_id',
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
			  'trn_store_orders.reward_points_used_store',
			  
			  'trn_store_orders.amount_before_applying_rp',
			  'trn_store_orders.amount_reduced_by_rp',
			  'trn_store_orders.amount_reduced_by_rp_store',
			  'trn_store_orders.trn_id',
			  'trn_store_orders.created_at',
			  'trn_store_orders.amount_reduced_by_coupon',
			  'trn_store_orders.order_type',
	  
			  'trn_store_customers.customer_id',
			  'trn_store_customers.customer_first_name',
			  'trn_store_customers.customer_last_name',
			  'trn_store_customers.customer_mobile_number',
			  'trn_store_customers.place',

              'sys_store_order_status.status',
	  
			 
	  
			 
	  
			)
			  ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
			  ->leftjoin('sys_store_order_status', 'sys_store_order_status.status_id', '=', 'trn_store_orders.status_id');
	  
			
			
			  $datefrom = $request->date_from;
			  $dateto = $request->date_to;
	  
			  $a1 = Carbon::parse($request->date_from)->startOfDay();
			  $a2  = Carbon::parse($request->date_to)->endOfDay();
	  
			  if (isset($request->date_from)) {
				$data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
			  }
	  
			  if (isset($request->date_to)) {
				$data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
			  }
	  
	  
			  if (isset($request->customer_mobile_number)) {


				$data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
			 
		  }
			 
	  
			  if (isset($request->delivery_boy_id)) {
				$data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
			  }
	  
			  if (isset($request->status_id)) {
				$data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
			  }
	  
			  if (isset($request->order_type)) {
				$data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
			  }
	  
			  if (isset($request->subadmin_id)) {
				$data = $data->where('trn_store_orders.subadmin_id', '=', $request->subadmin_id);
			  }
	  
			
			
	  
			$walletdata = $data->where('trn_store_orders.store_id',$store_id)
			    // ->where('trn_store_orders.reward_points_used','!=',NULL)
				// ->Orwhere('trn_store_orders.reward_points_used_store','!=',NULL)
                ->whereNotNull('trn_store_orders.reward_points_used_store');
			       
                if (isset($request->page)) {
                    $walletdata = $walletdata->orderBy('order_id', 'DESC')->paginate(10, ['data'], 'page', $request->page);
                } else {
                    $walletdata = $walletdata->orderBy('order_id', 'DESC')->paginate(10);
                }
	  //DD($request->store_id,$request->subadmin_id,$data);
      foreach($walletdata as $w)
      {
        $item_price=Trn_store_order_item::where('order_id',$w->order_id)->sum('total_amount');
        //$w->amount_before_applying_rp=$item_price+$$w->amount_reduced_by_rp??0+$w->packing_charge??0+$w->delivery_charge??0+$w->amount_reduced_by_rp_store??0+$w->amount_reduced_by_coupon??0;
        $w->amount_before_applying_rp=strval(number_format($item_price,2));

      }
      
      $resData['walletData'] = $walletdata;
      $resData['status'] = 1;
      $resData['message'] = "Success";
      return response($resData);
	  
			
  
	}
    public function getStoreReferrals(Request $request)
    {
        try{
            $data=array();
            $store_id=$request->store_id;
            $data['status']=1;
            $data['message']="Store level referrals fetched";
            $data['store_level_referrals']=Trn_store_referrals::leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_referrals.joined_by_id')
            ->where('store_id',$store_id)->get();
            return response($data);

        }
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];

            return response($response);
        }
    }
    public function listEnquiries(Request $request)
    {
        try {
            //$enquiries = Trn_customer_enquiry::with(['customer', 'varient'])->where('store_id',$request->store_id);
            // The 'with' method loads the relationships (customer, store, varient) to avoid additional queries.
            $enquiries = Trn_customer_enquiry::leftjoin('mst_store_product_varients','mst_store_product_varients.product_varient_id','=','trn_customer_enquiry.product_varient_id')
            ->leftjoin('trn_store_customers','trn_store_customers.customer_id','=','trn_customer_enquiry.customer_id')
            ->leftjoin('mst_stores','mst_stores.store_id','=','trn_customer_enquiry.store_id')
            ->leftjoin('mst_store_products','mst_store_products.product_id','=','mst_store_product_varients.product_id')
            ->select('trn_customer_enquiry.enquiry_id',
            'trn_customer_enquiry.product_varient_id',
            'trn_customer_enquiry.customer_id',
            'trn_customer_enquiry.visited_date',
            'trn_customer_enquiry.created_at',
            'mst_store_products.product_name',
            'mst_store_product_varients.variant_name',
            'mst_store_product_varients.product_id',
            'trn_store_customers.customer_first_name',
            'trn_store_customers.customer_last_name',
            'trn_store_customers.customer_mobile_number',
            'trn_customer_enquiry.store_id',
            'trn_customer_enquiry.created_at',
            'mst_stores.store_name'


        )->where('trn_customer_enquiry.store_id',$request->store_id)->latest();
    
            $data['status'] = 1;
            $data['message'] = "Enquiries retrieved successfully";
            if (isset($request->page)) {
                $data['enquiries'] = $enquiries->paginate(10, ['*'], 'page', $request->page);
            } else {
                $data['enquiries'] = $enquiries->paginate(10);
            }
    
            return response($data);
        } catch (\Exception $e) {
            return response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }
    public function enquiryReports(Request $request)
    {
        
        try {
            //$enquiries = Trn_customer_enquiry::with(['customer', 'varient'])->where('store_id',$request->store_id);
            // The 'with' method loads the relationships (customer, store, varient) to avoid additional queries.
            $enquiries = Trn_customer_enquiry::leftjoin('mst_store_product_varients','mst_store_product_varients.product_varient_id','=','trn_customer_enquiry.product_varient_id')
            ->leftjoin('trn_store_customers','trn_store_customers.customer_id','=','trn_customer_enquiry.customer_id')
            ->leftjoin('mst_stores','mst_stores.store_id','=','trn_customer_enquiry.store_id')
            ->leftjoin('mst_store_products','mst_store_products.product_id','=','mst_store_product_varients.product_id')
            ->select('trn_customer_enquiry.enquiry_id',
            'trn_customer_enquiry.product_varient_id',
            'trn_customer_enquiry.customer_id',
            'trn_customer_enquiry.visited_date',
            'trn_customer_enquiry.created_at',
            'mst_store_products.product_name',
            'mst_store_product_varients.variant_name',
            'mst_store_product_varients.product_id',
            'trn_store_customers.customer_first_name',
            'trn_store_customers.customer_last_name',
            'trn_store_customers.customer_mobile_number',
            'trn_customer_enquiry.store_id',
            'mst_stores.store_name',
            'trn_customer_enquiry.created_at',


        )->where('trn_customer_enquiry.store_id',$request->store_id);
        //dd(request('start_date'));
        if (request('start_date')!=NULL && request('end_date')!=NULL) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));

            $enquiries->whereBetween('trn_customer_enquiry.created_at', [$start_date, $end_date]);
        }
        if (request('customer_name')!=NULL) {
            $enquiries->where('trn_store_customers.customer_first_name', 'like', '%' . request('customer_name') . '%');
        }
        if (request('customer_mobile')!=NULL) {
            $enquiries->where('trn_store_customers.customer_mobile_number', 'like', '%' . request('customer_mobile') . '%');
        }
        if (request('product_name')!=NULL) {
            $enquiries->where('mst_store_products.product_name', 'like', '%' . request('product_name') . '%')->orWhere('mst_store_product_varients.variant_name', 'like', '%' . request('product_name') . '%');
        }
        
            $data['status'] = 1;
            $data['message'] = "Enquiries retrieved successfully";
            if (isset($request->page)) {
                $data['enquiries'] = $enquiries->latest()->paginate(10, ['*'], 'page', $request->page);
            } else {
                $data['enquiries'] = $enquiries->latest()->paginate(10);
            }
    
            return response($data);
        } catch (\Exception $e) {
            return response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }
    public function listYoutubeVideos(Request $request)
    {
          $store_id=$request->store_id;
         $store= Mst_store::find($store_id);
         if($store)
         {
            $links=DB::table('trn_store_youtube_videos')->where('store_id',$store_id)->get();

         }
         else
         {
            $links=[];
         }
           
    
            return response()->json([
                'status' => 1,
                'message' => 'Youtube links fetched successfully.',
                'data'=>$links
            ], 200);
        
    }
    public function storeYoutubeVideos(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'youtube_link' => 'required',
            ],
            [
                'youtube_link.required' => 'Youtube link required',
            ]
        );
    
        if (!$validator->fails()) {
            //$data = $request->except('_token');
            $values=[];
    
            $date = Carbon::now();
            $values = $request->youtube_link;
    
            $responseData = [];
            //return $values ;
            foreach ($values as $value) {
                $videoData = [
                    'youtube_link' => $value,
                    'store_id' => $request->store_id,
                    'youtube_title' => $value,
                    'youtube_status' => 1,
                ];
    
                DB::table('trn_store_youtube_videos')->insert($videoData);
    
                $responseData[] = $videoData;
            }
    
            return response()->json([
                'status' => 1,
                'message' => 'Youtube links added successfully.',
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
    }
    public function RemoveYoutubeVideos(Request $request)
	{
        $link_id=$request->link_id;
		$ytvideo = DB::table('trn_store_youtube_videos')->where('youtube_link_id',$link_id);
		if($ytvideo->first())
		{
			$ytvideo->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Youtube links deleted successfully.',
            ], 200);

		}
        else
        {
            return response()->json([
                'status' => 0,
                'message' => 'Youtube links not exist.',
            ], 200);

        }
		
		return redirect()->back()->with('status', 'Youtube video removed');
	}
}
