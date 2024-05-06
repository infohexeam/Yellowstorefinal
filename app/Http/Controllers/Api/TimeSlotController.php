<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

use App\Models\admin\Mst_store;
use App\Models\admin\Mst_Tax;

use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_business_types;

use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;

use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_store_agencies;

use App\Models\admin\Mst_product_image;
use App\Models\admin\Mst_store_product_varient;

use App\Models\admin\Trn_StoreDeliveryTimeSlot;


class TimeSlotController extends Controller
{
    public function listTimeSlots(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                { 
                    $store_id = $request->store_id;
                    if($data['timeSlotDetails']  = Trn_StoreDeliveryTimeSlot::
                        select('store_delivery_time_slot_id','store_id','time_start','time_end')
                        ->where('store_id',$store_id)->get())
                    {
                       
                        $data['status'] = 1;
                        $data['message'] = "success";
                        return response($data);
                    }
                    else{
                        $data['status'] = 0;
                        $data['message'] = "failed";
                        return response($data);
                    }
                }
                else
                {
                    $data['status'] = 0;
                    $data['message'] = "Store not found ";
                    return response($data);
                }

        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }
    }

    public function updateTimeSlots(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                { 
                    $store_id = $request->store_id;
                    $validator = Validator::make($request->all(),
                    [
                        'timeslotDetails.*.time_start'          => 'required',
                        'timeslotDetails.*.time_end'          => 'required',
                    ],
                    [
                        'timeslotDetails.*.time_start.required'        => 'Start time required',
                        'timeslotDetails.*.time_end.required'        => 'End time required',
                    ]);
                     
                        if(!$validator->fails() )
                        {
                            Trn_StoreDeliveryTimeSlot::where('store_id',$store_id)->delete();
                            foreach($request->timeslotDetails as $time){
                                $info = [
                                    'store_id'=> $store_id,
                                    'time_start'=>  $time['time_start'],
                                    'time_end'=> $time['time_end'],
                                    ];
                                    Trn_StoreDeliveryTimeSlot::insert($info);
                            }
                            $data['status'] = 1;
                            $data['message'] = "Time slot updated successfully.";
                            return response($data);
                        }
                        else
                        {
                            $data['status'] = 0;
                            $data['message'] = "failed";
                            $data['errors'] = $validator->errors();
                            return response($data);
                        }
                }
                else
                {
                    $data['status'] = 0;
                    $data['message'] = "Store not found ";
                    return response($data);
                }

        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }
    }
}
