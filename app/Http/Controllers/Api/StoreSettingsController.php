<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Response;
use Image;
use DB;
// use Hash;
use Carbon\Carbon;
use Crypt;
use Mail;
use PDF;
use App\Helpers\Helper;

use App\Models\admin\Mst_store;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_store_images;

use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_business_types;

use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;

use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Trn_ProductVariantAttribute;

use App\Models\admin\Mst_product_image;
use App\Models\admin\Mst_store_interior_images;
use App\Models\admin\Trn_store_setting;
use App\Models\admin\Trn_StoreTimeSlot;
use App\User;

use App\Models\admin\Mst_Subadmin_Detail;
use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Trn_store_order;

use App\Models\admin\Mst_StoreAppBanner;
use App\Models\admin\Mst_store_link_delivery_boy;
use App\Models\admin\Trn_RecentlyVisitedStore;
use App\Models\admin\Trn_StoreBankData;
use stdClass;

class StoreSettingsController extends Controller
{

    public function removeBanner(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_image_id) && Mst_store_images::find($request->store_image_id)) {
                $store_image_id = $request->store_image_id;
                if (Mst_store_images::where('store_image_id', $store_image_id)->delete()) {
                    $data['status'] = 1;
                    $data['message'] = "success";
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed.";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Banner not found.";
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
    public function removeStoreImage(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_image_id) && Mst_store_interior_images::find($request->store_image_id)) {
                $store_image_id = $request->store_image_id;
                if (Mst_store_interior_images::where('store_image_id', $store_image_id)->delete()) {
                    $data['status'] = 1;
                    $data['message'] = "success";
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed.";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Banner not found.";
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



    public function listDefaultSettings(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                $product_upload_limit=Mst_store::where('store_id',$store_id)->first()->product_upload_limit;
                $product_count=Mst_store_product_varient::where('store_id',$store_id)->count();
                if ($data['defaultStoreSettingsDetails']['service_amount']  = Trn_store_setting::where('store_id', $store_id)->get()) {
                    foreach($data['defaultStoreSettingsDetails']['service_amount'] as $sa)
                    {
                        $sa->delivery_charge=(int)$sa->delivery_charge;
                        $sa->packing_charge=(int)$sa->packing_charge;
                        $sa->delivery_charge=(string)$sa->delivery_charge;
                        $sa->packing_charge=(string)$sa->packing_charge;
                    }
                    $store_data = Mst_store::find($store_id);

                    $data['defaultStoreSettingsDetails']['order_number_prefix'] = $store_data->order_number_prefix;
                    $data['defaultStoreSettingsDetails']['service_area'] = $store_data->service_area;

                    if (isset($store_data->store_state_id))
                        $data['defaultStoreSettingsDetails']['store_state_id'] = $store_data->store_state_id;
                    else
                        $data['defaultStoreSettingsDetails']['store_state_id'] = 0;

                    if (isset($store_data->store_district_id))
                        $data['defaultStoreSettingsDetails']['store_district_id'] = $store_data->store_district_id;
                    else
                        $data['defaultStoreSettingsDetails']['store_district_id'] = 0;


                    if (isset($store_data->store_district_id))
                        $data['defaultStoreSettingsDetails']['town_id'] = $store_data->town_id;
                    else
                        $data['defaultStoreSettingsDetails']['town_id'] = 0;



                    $data['defaultStoreSettingsDetails']['business_type_id'] = $store_data->business_type_id;

                    $data['defaultStoreSettingsDetails']['store_mobile'] = $store_data->store_mobile;
                    $data['defaultStoreSettingsDetails']['email'] = $store_data->email;
                    $data['defaultStoreSettingsDetails']['place'] = $store_data->place;
                    $data['defaultStoreSettingsDetails']['store_pincode'] = $store_data->store_pincode;
                    $data['defaultStoreSettingsDetails']['store_website_link'] = $store_data->store_website_link;
                    $data['defaultStoreSettingsDetails']['store_primary_address'] = $store_data->store_primary_address;
                    $data['defaultStoreSettingsDetails']['store_qrcode'] = $store_data->store_qrcode;
                    $data['defaultStoreSettingsDetails']['subadmin_phone'] = Helper::storeSubadminPhone($request->store_id);
                    $data['defaultStoreSettingsDetails']['superadmin_phone'] = Helper::storeSuperadminPhone($request->store_id);
                    $data['defaultStoreSettingsDetails']['product_upload_limit'] = $product_upload_limit;
                    $data['defaultStoreSettingsDetails']['total_products_uploaded'] = $product_count;
                    $data['defaultStoreSettingsDetails']['immediate_option_status'] = $store_data->delivery_option_immediate;
                    $data['defaultStoreSettingsDetails']['slot_option_status'] = $store_data->delivery_option_slot;
                    $data['defaultStoreSettingsDetails']['future_option_status'] = $store_data->delivery_option_future;
                    $data['defaultStoreSettingsDetails']['delivery_start_time'] = $store_data->delivery_start_time;
                    $data['defaultStoreSettingsDetails']['delivery_end_time'] = $store_data->delivery_end_time;
                    $data['defaultStoreSettingsDetails']['pay_delivery_status'] = $store_data->pay_delivery_status;
                    $data['defaultStoreSettingsDetails']['collect_store_status'] = $store_data->collect_store_status;
                    $data['defaultStoreSettingsDetails']['immediate_delivery_text'] = $store_data->immediate_delivery_text;
                    //$data['defaultStoreSettingsDetails']['minimum_order_amount'] = Helper::calculateDeliveryCharge("75","50","500","450");
                    $data['defaultStoreSettingsDetails']['minimum_stock_alert_status'] = $store_data->minimum_stock_alert_status;
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


    public function updateSettings(Request $request)
    {
        $data = array();
        //print_r($request->all());
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                $data2 = array();

                if (isset($request->order_number_prefix)) {
                    $storePrefix = Mst_store::where('order_number_prefix', $request->order_number_prefix)->where('store_id', '!=', $store_id)->count();
                    if ($storePrefix > 0) {
                        $data['status'] = 0;
                        $data['message'] = "Order prefix already taken";
                        return response($data);
                    }
                }

                $validator = Validator::make(
                    $request->all(),
                    [
                        'service_area' => 'required',
                        // 'store_district_id' => 'required|numeric',
                        // 'town_id' => 'required|numeric',
                        /// 'business_type_id' => 'required|numeric',
                        'service_amount.*.service_start'          => 'required',
                        'service_amount.*.service_end'          => 'required',
                        'service_amount.*.delivery_charge'          => 'required',
                        'service_amount.*.packing_charge'          => 'required',

                    ],
                    [
                        'service_amount.*.service_start.required'        => 'Service starting km  required',
                        'service_amount.*.service_end.required'        => 'Service ending km required',
                        'service_amount.*.delivery_charge.required'        => 'Delivery charge required',
                        'service_amount.*.packing_charge.required'        => 'Packing charge required',
                        'service_area.required'        => 'Service area required',
                        'store_district_id.required'        => 'District required',
                        'town_id.required'        => 'Town required',
                        'business_type_id.required'        => 'Business type required',
                    ]
                );

                if (!$validator->fails()) {
                    //  echo $request->service_area;die;
                    if (isset($request->service_area))
                        $data2['service_area'] = $request->service_area;


                    if (isset($request->order_number_prefix))
                        $data2['order_number_prefix'] = $request->order_number_prefix;
                    
                    if (isset($request->immediate_delivery))
                        $data2['delivery_option_immediate'] = $request->immediate_delivery;

                    if (isset($request->slot_delivery))
                        $data2['delivery_option_slot'] = $request->slot_delivery;
                        
                    if (isset($request->future_delivery))
                        $data2['delivery_option_future'] = $request->future_delivery;
                    if (isset($request->delivery_start_time))
                    {
                        $data2['delivery_start_time']=$request->delivery_start_time??'7:00';
                    }
                    if (isset($request->delivery_end_time))
                    {
                        $data2['delivery_end_time']=$request->delivery_end_time??'19:00';
                    }
                    if (isset($request->pay_delivery_status))
                    {
                        $data2['pay_delivery_status']=1;//$request->pay_delivery_status;
                    }
                    if (isset($request->collect_store_status))
                    {
                        $data2['collect_store_status']=$request->collect_store_status;
                    }
                    if (isset($request->immediate_delivery_text))
                    {
                        $data2['immediate_delivery_text']=$request->immediate_delivery_text;
                    }
                    if (isset($request->minimum_order_amount))
                    {
                        $data2['minimum_order_amount']=$request->minimum_order_amount;
                    }
                    if (isset($request->minimum_stock_alert_status))
                    {
                        $data2['minimum_stock_alert_status']=$request->minimum_stock_alert_status;
                    }
                    

                    Mst_store::where('store_id', $store_id)->update($data2);
                    // echo "here";die;
                    Trn_store_setting::where('store_id', $store_id)->delete();

                    foreach ($request->service_amount as $val) {

                        $ss = new Trn_store_setting;
                        $ss->store_id = $store_id;
                        $ss->service_start = $val['service_start'];
                        $ss->service_end = $val['service_end'];
                        $ss->delivery_charge = $val['delivery_charge'];
                        $ss->packing_charge = $val['packing_charge'];
                        $ss->minimum_order_amount = $val['minimum_order_amount']??0;
                        $ss->reduction_percentage = $val['reduction_percentage']??0;
                        $ss->save();

                        //     $data5 = [
                        //         'store_id' => $store_id,
                        //         'service_start' => $val['service_start'],
                        //         'service_end' => $val['service_end'],
                        //         'delivery_charge' => $val['delivery_charge'],
                        //         'packing_charge' => $val['packing_charge'],
                        //     ];
                        //   // dd($data5);

                        //   if(Trn_store_setting::create($data5))
                        //   {
                        //       echo "here";die;
                        //   }
                        //   else
                        //   {
                        //       echo "sds";die;

                        //   }


                    }

                    $data['status'] = 1;
                    $data['onBoardingStatus'] = Helper::onBoardingStatus($store_id);

                    $data['message'] = " Store settings updated";
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                    $data['errors'] = $validator->errors();
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


    public function listWorkingDays(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                $workDayCount = Trn_StoreTimeSlot::where('store_id', $store_id)->count();
                if ($workDayCount > 0) {
                    if ($data['workingDayDetails']  = Trn_StoreTimeSlot::select('store_time_slot_id', 'store_id', 'day', 'time_start', 'time_end')
                        ->where('store_id', $store_id)->get()
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
                    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                    foreach ($days as $day) {
                        $info = [
                            'store_id' => $store_id,
                            'day' =>  $day,
                            'time_start' =>  null,
                            'time_end' => null,
                        ];
                        Trn_StoreTimeSlot::insert($info);
                    }

                    if ($data['workingDayDetails']  = Trn_StoreTimeSlot::select('store_time_slot_id', 'store_id', 'day', 'time_start', 'time_end')
                        ->where('store_id', $store_id)->get()
                    ) {

                        $data['status'] = 1;
                        $data['message'] = "success";
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                        return response($data);
                    }
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

    public function updateBankDetails(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {

                $sDAta = Mst_store::find($request->store_id);
                $sBankDAta = Trn_StoreBankData::where('store_id', $request->store_id)->where('status', 1)->count();
                if ($sBankDAta == 0) {

                    $curl = curl_init();

                    if (isset($sDAta->store_mobile)) {
                        $store_mobile = $sDAta->store_mobile;
                    } else {
                        $store_mobile = '0000000000';
                    }

                    if (isset($sDAta->email)) {
                        $email = $sDAta->email;
                    } else {
                        $email = 'test@mail.com';
                    }
                    $string2 = str_replace(' ', '-', $sDAta->store_name);
                    $string3 = str_replace('-', '', $string2);

                    $vendorId = preg_replace('/[^A-Za-z0-9\-]/', '', $string3) . substr($request->acc_no, strlen($request->acc_no) - 4);
                    // dd($vendorId);
                    $string4 = str_replace('-', '', $sDAta->store_name);

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.cashfree.com/api/v2/easy-split/vendors',
                        //CURLOPT_URL => 'https://test.cashfree.com/api/v2/easy-split/vendors',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => '{
                        "email": "' . $email . '",
                        "status": "ACTIVE",
                        "bank": 
                          {
                            "accountNumber": "' . $request->acc_no . '",
                            "accountHolder": "' . $request->account_holder . '",
                            "ifsc": "' . $request->ifsc . '"
                          },
                         
                        "phone": "' . $store_mobile . '",
                        "name": "' . preg_replace('/[0-9]+/', '', $string4)  . '",
                        "id": "' . $vendorId . '",
                        "settlementCycleId": 2
                      }',
                        CURLOPT_HTTPHEADER => array(
                            'x-client-id: 165253d13ce80549d879dba25b352561',
                            'x-client-secret: bab0967cdc3e5559bded656346423baf0b1d38c4',
                            'x-api-version: 2021-05-21',
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    $jData = json_decode($response);
                    if (!isset($jData->subCode)) {
                        $store_id = $request->store_id;
                        $data = new Trn_StoreBankData;
                        $data->store_id = $store_id;
                        $data->account_number = $request->acc_no;
                        $data->ifsc = $request->ifsc;
                        $data->account_holder = $request->account_holder;
                        $data->status = 1;

                        $data->phone = $store_mobile;
                        $data->vendor_name = $sDAta->store_name;
                        $data->vendor_id = $vendorId;
                        $data->settlement_cycle_id = 2;

                        $data->save();
                        return  $response = ['status' => 1, 'message' => 'Bank details updated'];
                    } else {
                        return  $response = ['status' => $jData->subCode, 'message' => $jData->message];
                    }
                } else {

                    $sBankDAta = Trn_StoreBankData::where('store_id', $request->store_id)->where('status', 1)->orderBy('store_bank_data_id', 'DESC')->first();


                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                         CURLOPT_URL => 'https://api.cashfree.com/api/v2/easy-split/vendors/' . $sBankDAta->vendor_id,
                       // CURLOPT_URL => 'https://test.cashfree.com/api/v2/easy-split/vendors/' . $sBankDAta->vendor_id,
                        
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'PUT',
                        CURLOPT_POSTFIELDS => '{
                            "bank": 
                                {
                                    "accountNumber": "' . $request->acc_no . '",
                                    "accountHolder": "' . $request->account_holder . '",
                                    "ifsc": "' . $request->ifsc . '"
                                },
                            "settlementCycleId": 2
                            }',
                        CURLOPT_HTTPHEADER => array(
                            'X-Client-Id: 165253d13ce80549d879dba25b352561',
                            'X-Client-Secret: bab0967cdc3e5559bded656346423baf0b1d38c4',
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $sBankDAta = Trn_StoreBankData::where('store_id', $request->store_id)->where('status', 1)->orderBy('store_bank_data_id', 'DESC')->first();


                    $jData = json_decode($response);
                    if ($jData->status == "OK") {
                        $store_id = $request->store_id;
                        $data = Trn_StoreBankData::find($sBankDAta->store_bank_data_id);
                        $data->account_number = $request->acc_no;
                        $data->ifsc = $request->ifsc;
                        $data->account_holder = $request->account_holder;
                        $data->update();
                        return  $response = ['status' => 1, 'message' => 'Bank details updated'];
                    } else {
                        return $response = ['status' => 0, 'message' => 'Unable to update bank details' . $jData->message];
                    }
                }
            } else {
                return $response = ['status' => 0, 'message' => 'Store not found'];
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function updateWorkingDays(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                // $validator = Validator::make($request->all(),
                // [
                //    // 'workingDays.*.day'          => 'required',
                //    // 'workingDays.*.time_start'          => 'required',
                //    // 'workingDays.*.time_end'          => 'required',
                // ],
                // [
                //    // 'workingDays.*.day.required'        => 'Day name required',
                //    // 'workingDays.*.time_start.required'        => 'Start time required',
                //    // 'workingDays.*.time_end.required'        => 'End time required',
                //]);

                // if(!$validator->fails() )
                // {
                Trn_StoreTimeSlot::where('store_id', $store_id)->delete();
                foreach ($request->workingDays as $time) {
                    $info = [
                        'store_id' => $store_id,
                        'day' =>  $time['day'],
                        'time_start' =>  $time['time_start'],
                        'time_end' => $time['time_end'],
                    ];
                    Trn_StoreTimeSlot::insert($info);
                }
                $data['status'] = 1;
                $data['onBoardingStatus'] = Helper::onBoardingStatus($store_id);
                $data['message'] = "Working days updated successfully.";
                return response($data);
                // }
                // else
                // {
                //     $data['status'] = 0;
                //     $data['message'] = "All fields required";
                // $errors = $validator->errors();
                // $e = array();
                // foreach ($validator->errors() as $error){
                //     dd($error)
                // }
                //$data['errors'] = $errors;
                return response($data);
                // }
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


    public function listStoreInfo(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                if ($data['storeDetails']  = Mst_store::where('store_id', $store_id)
                    ->select(
                        'store_id',
                        'store_name',
                        'store_code',
                        'store_contact_person_name',
                        'store_name_slug',
                        'store_contact_person_phone_number',
                        'business_type_id',
                        'store_country_id',
                        'store_district_id',
                        'store_state_id',
                        'town_id',
                        'store_username',
                        'store_commision_percentage',
                        'store_mobile',
                        'email',
                        'place',
                        'store_pincode',
                        'store_website_link',
                        'store_primary_address',
                        'town_id',
                        'gst',
                        'subadmin_id',
                        'upi_id',
                        'profile_image',
                        'place',
                        'longitude',
                        'latitude',
                        'store_referral_id',
                        'profile_description',
                        'youtube_account_link',
                        'facebook_account_link',
                        'instagram_account_link',
                        'snapchat_account_link'
                    )
                    ->first()
                ) {
                    $subadminData = Mst_Subadmin_Detail::where('subadmin_id', $data['storeDetails']->subadmin_id)->first();
                    $data['storeDetails']['subadmin_phone'] = Helper::storeSubadminPhone($request->store_id);
                    $data['storeDetails']['superadmin_phone'] = Helper::storeSuperadminPhone($request->store_id);
                    $data['storeDetails']['store_qrcode'] = @$data['storeDetails']->store_name_slug . "-" . @$data['storeDetails']->store_mobile;
                    $cn=DB::table('sys_countries')->where('country_id',$data['storeDetails']->store_country_id)->first();
                    $data['storeDetails']['country_name']=$cn->country_name??'';
                    $st=DB::table('sys_states')->where('state_id',$data['storeDetails']->store_state_id)->first();
                    $data['storeDetails']['state_name']=$st->state_name??'';
                    $di=DB::table('mst_districts')->where('district_id',$data['storeDetails']->store_district_id)->first();
                    $data['storeDetails']['district_name']=$di->district_name??'';
                    $pin=DB::table('mst_towns')->where('town_id',$data['storeDetails']->town_id)->first();
                    $data['storeDetails']['pincode']=$pin->town_name??'';
                    if (isset($data['storeDetails']->profile_image))
                        $data['storeDetails']['profile_image'] = '/assets/uploads/store_images/images/' . $data['storeDetails']->profile_image;

                    $data['storeDetails']['store_images'] = Mst_store_images::where('store_id', $store_id)
                        ->select('store_image_id', 'store_image', 'store_id', 'default_image')
                        ->get();
                    foreach ($data['storeDetails']['store_images'] as $img) {
                        $img->store_image = '/assets/uploads/store_images/images/' . $img->store_image;
                    }
                    
                    $data['storeDetails']['store_interior_images'] = Mst_store_interior_images::where('store_id', $store_id)
                        ->select('store_image_id', 'store_image', 'store_id', 'default_image')
                        ->get();
                    foreach ($data['storeDetails']['store_interior_images'] as $img) {
                        $img->store_image = '/assets/uploads/store_images/images/' . $img->store_image;
                    }

                    $data['bankDetail'] = [];

                    $data['bankDetail'] = Trn_StoreBankData::where('store_id', $store_id)->get();




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

    public function updateStoreInfo(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                $validator = Validator::make(
                    $request->all(),
                    [
                        'store_name' => 'required',
                        'store_contact_person_name'          => 'required',
                        'store_contact_person_phone_number'          => 'required',
                        'business_type_id'          => 'required',
                        'store_country_id'          => 'required',
                        'store_state_id'          => 'required',
                        'store_district_id'          => 'required',
                        'town_id'          => 'required',
                        'store_username'          => 'required',
                        'store_mobile'          => 'required',
                        //'email'          => 'required',
                        'place'          => 'required',
                        'store_pincode'          => 'required',
                        'store_primary_address'          => 'required',
                        'profile_description'          => 'required',
                        'store_images.*.store_image'          => 'required|max:2048',
                        'store_images.*.default_image'          => 'required|max:2048',

                    ],
                    [
                        'store_name.required'        => 'Store name required',
                        'store_name.unique'        => 'Store name exists',
                        'store_contact_person_name.required'        => 'Contact person required',
                        'store_contact_person_phone_number.required'        => 'Contact person number required',
                        'business_type_id.required'        => 'Business type required',
                        'store_country_id.required'        => 'Country required',
                        'store_state_id.required'        => 'State required',
                        'store_district_id.required'        => 'District required',
                        'town_id.required'        => 'Town required',
                        'store_username.required'        => 'Username required',
                        'store_mobile.required'        => 'Username required',
                        'place.required'        => 'Place required',
                        'store_pincode.required'        => 'Store pincode required',
                        'store_primary_address.required'        => 'Primary address required',
                        'store_images.*.store_image.required'        => 'Image required',
                        'store_images.*.default_image.required'        => 'Default status required',
                    ]
                );

                if (!$validator->fails()) {

                    $data2['store_name'] = $request->store_name;
                    $data2['store_contact_person_name'] = $request->store_contact_person_name;
                    $data2['store_contact_person_phone_number'] = $request->store_contact_person_phone_number;
                    $data2['business_type_id'] = $request->business_type_id;
                    $data2['store_country_id'] = $request->store_country_id;
                    $data2['store_state_id'] = $request->store_state_id;
                    $data2['store_district_id'] = $request->store_district_id;
                    $data2['town_id'] = $request->town_id;
                    $data2['store_username'] = $request->store_username;
                    $data2['gst'] = $request->gst;
                    $data2['store_mobile'] = $request->store_mobile;
                    $data2['place'] = $request->place;
                    $data2['email'] = $request->email;
                    $data2['store_website_link'] = $request->store_website_link;
                    $data2['store_pincode'] = $request->store_pincode;
                    $data2['store_primary_address'] = $request->store_primary_address;
                    $data2['upi_id'] = $request->upi_id;
                    // $data2['store_commision_percentage'] = $request->commision_percentage;

                    $data2['latitude'] = $request->latitude;
                    $data2['longitude'] = $request->longitude;
                    $data2['place'] = $request->place;
                    $data2['profile_description']=$request->profile_description;
                    $data2['youtube_account_link']=$request->youtube_account_link;
                    $data2['facebook_account_link']=$request->facebook_account_link;
                    $data2['instagram_account_link']=$request->instagram_account_link;
                    $data2['snapchat_account_link']=$request->snapchat_account_link;



                    $store = Mst_store::find($store_id);
                    $filenamePro = $store->profile_image;

                    /*if ($request->hasFile('profile_image')) {

                        $filePro = $request->file('profile_image');
                        $filenamePro = $filePro->getClientOriginalName();
                        $filePro->move('assets/uploads/store_images/images', $filenamePro);
                    }*/
                    if ($request->hasFile('profile_image')) {
                        
                            $filePro = $request->file('profile_image');
                            $destination_path='assets/uploads/store_images/images/';
                            $filenamePro = uniqid('store_profile_image_');
                                        
                            // Use Intervention Image to open and manipulate the image
                            $resizedImage = Image::make($filePro->getRealPath())->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                            });

                            $originalSize = $resizedImage->filesize();

                                    // Determine compression quality based on original size
                            if ($originalSize > 50000)
                            { // If original size > 50kb
                            $quality = 50;
                            } 
                            elseif ($originalSize > 30000) { // If original size > 30kb
                            $quality = 60;
                            } 
                            else
                            { // If original size <= 30kb
                            $quality = 100;
                            }


                                    // Convert the image to JPG format
                            $resizedImage->encode('jpg',$quality);
                            $resizedImage->save($destination_path . $filenamePro . '.jpg'); // Adjust quality as needed

                    // dd($resizedImage);
                    // dd($resizedImage->filesize());
                    }
                    $data2['profile_image'] = @$filenamePro.'.jpg';

                    if (Mst_store::where('store_id', $store_id)->update($data2)) {
                        /*if ($files = $request->file('store_images')) {
                            $filename = "";

                            //   Mst_store_images::where('store_id', $store_id)->delete();

                            foreach ($files as $file) {
                                $filename = $file->getClientOriginalName();
                                $file->move('assets/uploads/store_images/images/', $filename);
                                $info = [
                                    'store_id' => $store_id,
                                    'store_image' =>  $filename,
                                    'default_image' =>  0,
                                ];
                                Mst_store_images::insert($info);
                            }
                        }*/
                        if ($files = $request->file('store_images')) {
                            // Mst_store_images::where('store_id', $store_id)->delete();
                        
                            foreach ($files as $file) {
    
                                $filename = uniqid('store_image_');
                                $destination_path = 'assets/uploads/store_images/images/';
                                //$destination_path = public_path('/assets/uploads/store_images/images');
                      
                        
                           
                                                          // Use Intervention Image to open and manipulate the image
                                $resizedImage = Image::make($file->getRealPath())->resize(300, null, function ($constraint) {
                                                  $constraint->aspectRatio();
                                                  $constraint->upsize();
                                              });
                                            
                                $originalSize = $resizedImage->filesize();
                                                  
                                                          // Determine compression quality based on original size
                                if ($originalSize > 50000)
                                        { // If original size > 50kb
                                  $quality = 50;
                                } 
                                elseif ($originalSize > 30000) 
                                { // If original size > 30kb
                                  $quality = 60;
                                } 
                                else
                                { // If original size <= 30kb
                                  $quality = 100;
                                }
                                                  
                                            
                                // Convert the image to JPG format
                                $resizedImage->encode('jpg',$quality);
                                $resizedImage->save($destination_path . $filename . '.jpg'); // Adjust quality as needed
                                //$file->move('assets/uploads/store_images/images/', $filename);
                                $info = [
                                    'store_id' => $store_id,
                                    'store_image' => $filename . '.jpg',
                                    'default_image' =>  0,
                                ];
                                Mst_store_images::insert($info);
                            }
                        }
                        if ($files_int = $request->file('store_interior_images')) {
                            // Mst_store_images::where('store_id', $store_id)->delete();
                        
                            foreach ($files_int as $file) {
                                $extension = $file->getClientOriginalExtension();
                                $filename = uniqid('store_interior_image_') . '.' . $extension;
                                $$filename = uniqid('store_image_');
                                $destination_path = 'assets/uploads/store_images/images/';
                                //$destination_path = public_path('/assets/uploads/store_images/images');
                      
                        
                           
                                                          // Use Intervention Image to open and manipulate the image
                                $resizedImage = Image::make($file->getRealPath())->resize(300, null, function ($constraint) {
                                                  $constraint->aspectRatio();
                                                  $constraint->upsize();
                                              });
                                            
                                $originalSize = $resizedImage->filesize();
                                                  
                                                          // Determine compression quality based on original size
                                if ($originalSize > 50000)
                                        { // If original size > 50kb
                                  $quality = 50;
                                } 
                                elseif ($originalSize > 30000) 
                                { // If original size > 30kb
                                  $quality = 60;
                                } 
                                else
                                { // If original size <= 30kb
                                  $quality = 100;
                                }
                                                  
                                            
                                // Convert the image to JPG format
                                $resizedImage->encode('jpg',$quality);
                                $resizedImage->save($destination_path . $filename . '.jpg'); // Adjust quality as needed
                                $info = [
                                    'store_id' => $store_id,
                                    'store_image' =>  $filename . '.jpg',
                                    'default_image' =>  0,
                                ];
                                Mst_store_interior_images::insert($info);
                            }
                        }
                        
                    }

                    $storeImgs = Mst_store_images::where('store_id', $store_id)->get();
                    foreach ($storeImgs as $img) {
                        $img->store_image = '/assets/uploads/store_images/images/' . $img->store_image;
                    }
                    $storeInteriorImgs = Mst_store_interior_images::where('store_id', $store_id)->get();
                    foreach ($storeInteriorImgs as $img) {
                        $img->store_image = '/assets/uploads/store_images/images/' . $img->store_image;
                    }


                    $data['status'] = 1;
                    $data['storeImgs'] = $storeImgs;
                    $data['storeInteriorImgs']=$storeInteriorImgs;

                    $data['message'] = "Store Info updated successfully.";
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                    $data['errors'] = $validator->errors();
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

    public function updatePassword(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                if (isset($request->store_admin_id) && Trn_StoreAdmin::find($request->store_admin_id)) {

                    $store_id = $request->store_id;
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'old_password'          => 'required',
                            'store_admin_id'          => 'required',

                            'password' => 'required|confirmed',

                        ],
                        [
                            'store_admin_id.required'        => 'store admin id required',
                            'old_password.required'        => 'Old password required',
                            'password.required'        => 'Password required',
                            'password.confirmed'        => 'Passwords not matching',
                        ]
                    );

                    if (!$validator->fails()) {


                        $storeData = Trn_StoreAdmin::where('store_id', $request->store_id)->where('store_admin_id', $request->store_admin_id)->first();

                        //   echo $request->old_password;die;
                        //     if(Hash::check($request->old_password, $storeData->password))
                        //     {
                        //         echo "yes";die;
                        //     }
                        //     else
                        //     {
                        //     echo "here";die;
                        //     }



                        if (Hash::check($request->old_password, $storeData->password)) {

                            $data20 = [
                                'password'      => Hash::make($request->password),
                            ];
                            Trn_StoreAdmin::where('store_admin_id', $storeData->store_admin_id)->update($data20);
                            Mst_store::where('store_id', $request->store_id)->update($data20);

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
                        $data['message'] = "failed";
                        $data['errors'] = $validator->errors();
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Store not found ";
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

    public function dashboard(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                $store  =    Mst_store::find($request->store_id);
                $storeAdmData = Trn_StoreAdmin::where('store_id', $store->store_id)->where('role_id', 0)->first();
                $today = Carbon::now()->addDays(3);
                $now =Carbon::now();
                $dateExp = Carbon::parse(@$storeAdmData->expiry_date);
                $diff = $dateExp->diffInDays($now) + 1; //14
                                                
                $todayDate =  Carbon::now()->toDateString();

                if(@$diff == 1){
                    $dayString = 'day';
                }else{
                    $dayString = 'days';
                }

            if(@$storeAdmData->expiry_date == $todayDate)
            {
                $expireMsgString = "Store expires today";
                $expiredDays = @$diff;
            
            }elseif($todayDate > @$storeAdmData->expiry_date)
            {

                $expireMsgString = 'Store expired on ' . @$storeAdmData->expiry_date . " - " . @$diff .'days before';
                // $expiredDays = @$diff;
                $expiredDays = '0';
                

            }else{
                    if (@$diff <= 3) 
                    {
                        $expireMsgString = 'This account expires in ' . @$diff . " " . @$dayString;
                        $expiredDays = @$diff;
                    }else{
                        $expireMsgString = '';
                        $expiredDays = '1';
                    }

            }



                $data['expireMsgString'] = $expireMsgString;

                if ($expiredDays <= 0) {
                    $data['isExpired'] = 1;
                } else {
                    $data['isExpired'] = 0;
                }







                $recentvisitCountToday = Trn_RecentlyVisitedStore::whereDate('created_at', Carbon::today())
                    ->where('store_id', $request->store_id)->count();

                $recentvisitCountWeek = Trn_RecentlyVisitedStore::whereBetween('created_at', [Carbon::now()->startOfWeek()->format('Y-m-d'),Carbon::now()->endOfWeek()->format('Y-m-d')])
                    ->where('store_id', $request->store_id)->count();
              
                $recentvisitCountMonth = Trn_RecentlyVisitedStore::whereBetween('created_at',[Carbon::now()->startOfMonth()->format('Y-m-d'),Carbon::now()->endOfMonth()->format('Y-m-d')])
                    ->where('store_id',$request->store_id)->count();

                // echo $recentvisitCountToday." -- ".$recentvisitCountWeek." -- ".$recentvisitCountMonth;


                $data['storeVisitToday'] = $recentvisitCountToday;
                $data['storeVisitWeek'] = $recentvisitCountWeek;
                $data['storeVisitMonth'] = $recentvisitCountMonth;

                $storeProductData = Mst_store_product::select('product_cat_id')
                    ->where('store_id', '=', $store_id)
                    ->orderBy('product_id', 'DESC')
                    ->get()->unique('product_cat_id')->pluck('product_cat_id')->toArray();

                $catCount = Mst_categories::whereIn('category_id', $storeProductData)->count();

                $data['categoriesCount'] = $catCount;


                // $data['categoriesCount'] = Mst_categories::count();
                $data['totalNumberOfProducts'] =  Mst_store_product::join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                    ->where('mst_store_products.is_removed', 0)
                    ->where('mst_store_products.store_id', $store_id)
                    ->orderBy('mst_store_products.product_id', 'DESC')->count();

                // $data['totalNumberOfProducts'] = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                //     ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                //     ->where('mst_store_products.is_removed', 0)
                //     ->where('mst_store_product_varients.is_removed', 0)->where('mst_store_products.store_id', '=', $store_id)
                //     ->orderBy('mst_store_products.product_id', 'DESC')->count();


                $data['totalNumberOfOrders'] = Trn_store_order::where('store_id', '=', $store_id)->get()->count();
                $data['totalSales'] = Trn_store_order::where('store_id', '=', $store_id)->where('status_id', '!=', 5)->sum('product_total_amount');
                $data['todaysSale'] = Trn_store_order::where('store_id', '=', $store_id)->where('status_id', '!=', 5)->whereDate('created_at', Carbon::today())->sum('product_total_amount');
                $data['dailySalesCount'] = Trn_store_order::where('store_id', '=', $store_id)->whereDate('created_at', Carbon::today())->count();
                $data['deliveryBoys'] =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
                    //->where('mst_delivery_boys.delivery_boy_status', 1)
                    ->where('mst_store_link_delivery_boys.store_id', $request->store_id)
                    ->count();
                $data['totalIssues'] =  \DB::table("mst_disputes")->where('store_id', '=', $store_id)->count();
                $data['currentIssues'] = \DB::table("mst_disputes")->where('dispute_status', '=', 2)->where('store_id', '=', $store_id)->count();
                $data['newIssues'] = \DB::table("mst_disputes")->where('dispute_status', '=', 2)->where('store_id', '=', $store_id)->whereDate('created_at', Carbon::today())->count();

                $banners =  Mst_StoreAppBanner::where('town_id', @$store->town_id)->orWhere('town_id', null)
                    ->select('banner_id', 'town_id', 'image')
                    ->where('status',1)
                    ->get();
                foreach ($banners as $b) {
                    $b->image = 'assets/uploads/store_banner/' . $b->image;
                }
                $data['dashboardDetails'] = $banners;
                $data['status'] = 1;
                $data['message'] = "success.";
                return response($data);
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
    public function newOrders(Request $request)
    {
        $dataRes=array();
        $store_id=$request->store_id;
      $newOrders=Trn_store_order::whereDate('created_at', Carbon::today())->where('store_id',$store_id)->where('status_id',1)->whereNull('TEST')->latest()->limit(4)->get()->map(function($data){
        //$subdata=json_decode($data->data);
        $qry['order_id']= $data->order_id;
        $qry['order_number']=$data->order_number;
        $qry['TEST']= 0;
        $qry['total']= (float)$data->product_total_amount;
        $qry['updated_at']= $data->updated_at->diffForHumans();
        return $qry;
      });
      if(count($newOrders)>0)
      {
        $dataRes['status']=1;
        $dataRes['message']="Orders fetched";
        $dataRes['newOrders']=$newOrders;
        return response($dataRes);

      }
      else
      {
        $dataRes['status']=0;
        $dataRes['message']="No Orders fetched";
        $dataRes['newOrders']=[];
        return response($dataRes);

      }
  
    }

    public function newOrdersAll(Request $request)
    {
      $dataRes=array();
      $newOrders=Trn_store_order::whereDate('created_at', Carbon::today())->where('store_id',$request->store_id)->where('status_id',1)->whereNull('TEST')->latest()->get()->map(function($data){
        //$subdata=json_decode($data->data);
        $qry['order_id']= $data->order_id;
        $qry['order_number']=$data->order_number;
        $qry['TEST']= 0;
        $qry['total']= (float)$data->product_total_amount;
        $qry['updated_at']= $data->updated_at->diffForHumans();
        return $qry;
      });
      if(count($newOrders)>0)
      {
        $dataRes['status']=1;
        $dataRes['newOrders']=$newOrders;
        return response($dataRes);

      }
      else
      {
        $dataRes['status']=0;
        $dataRes['newOrders']=[];
        return response($dataRes);

      }
      
  
    }
    public function getMinimumStockProducts(Request $request)
    {
        $dataRes = array();

        try {
            $store_id=$request->store_id;
            $products=Helper::minimumStockProducts($store_id);
            $minStockProducts=$products->map(function($data){
            $var=Mst_store_product_varient::where('product_varient_id',$data['product_varient_id'])->first();

            $product['product_name']=$data['variant_name'];
            $product['prodcut_id']=$data['product_id'];
            $product['product_varient_id']=$data['product_varient_id'];
            $product['stock_count']=$var['stock_count'];
            $product['minimum_stock']=$data['min_stock']??0;
            return $product;
    });
            if(count($minStockProducts)>0)
            {
                $dataRes['status']=1;
                $dataRes['minimumStockProducts']=$minStockProducts;
                return response($dataRes);
                
            }
            else
            {
                $dataRes['status']=1;
                $dataRes['minimumStockProducts']=[];
                return response($dataRes);

            }
        }
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }

    }
}
