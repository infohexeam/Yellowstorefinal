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
use App\Helpers\Helper;

use App\Models\admin\Mst_store;
use App\Models\admin\Mst_Tax;

use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_business_types;

use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;

use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_store_agencies;

use App\Models\admin\Mst_product_image;

use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Trn_order_invoice;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Mst_store_link_delivery_boy;
use App\Models\admin\Mst_order_link_delivery_boy;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Mst_Issues;
use App\Models\admin\Mst_dispute;
use App\Models\admin\Sys_IssueType;
use App\Models\admin\Trn_CustomerDeviceToken;



class DisputeController extends Controller
{
    public function listDispute(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                {
                    $store_id = $request->store_id;

                    $validator = Validator::make($request->all(),
                    [
                        'dispute_status'          => 'required',
                    ],
                    [
                        'dispute_status.required'        => 'Dispute status required',
                    ]);
                     
                        if(!$validator->fails() )
                        {
                                if($request->dispute_status == 0)
                                {
                                    if($disputesData  = \DB::table("mst_disputes")->where('store_id',$request->store_id)->select("*"))
                                    {
                                        if(isset($request->type))
                                        {
                                            if($request->type == 1){
                                                $disputesData = $disputesData->where('dispute_status','=',2)->whereDate('created_at', Carbon::today());
                                            }
                                            elseif($request->type == 2){
                                                $disputesData = $disputesData->where('dispute_status','=',2);
                                            }
                                        }
                                        $disputesData = $disputesData->orderBy('dispute_id','DESC')->get();
                                        $data['disputeDetails'] = $disputesData;
                                        foreach($data['disputeDetails'] as $dispute){
                                            $issue = Mst_Issues::find($dispute->issue_id);
                                            $dispute->issue = $issue->issue;
                                            $customer = Trn_store_customer::find($dispute->customer_id);
                                            $dispute->customer_name = @$customer->customer_first_name." ".@$customer->customer_last_name;
                                            
                                            $issueType = Sys_IssueType::find($dispute->issue_id);

                                            if(isset($issueType->issue_type))
                                            $dispute->issue_type = @$issueType->issue_type;
                                            else
                                            $dispute->issue_type = '';
                                            
                                          
                                            
                                              if(isset($dispute->discription))
                                               $dispute->discription = $dispute->discription; 
                                               else
                                               $dispute->discription = ''; 
            
                                               
                                               if(isset($dispute->store_response))
                                               $dispute->store_response = $dispute->store_response; 
                                               else
                                               $dispute->store_response = '';
                                           
                                           

                                        }
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
                                    if( $disputesData  = \DB::table("mst_disputes")->where('store_id',$request->store_id)
                                    ->where('dispute_status',$request->dispute_status)
                                    )
                                    {
                                        if(isset($request->type))
                                        {
                                            if($request->type == 1){
                                                $disputesData = $disputesData->whereDate('created_at', Carbon::today());
                                            }
                                        }
                                        
                                        
                                        
                                        $disputesData = $disputesData->select("*")->orderBy('dispute_id','DESC')->get();
                                        $data['disputeDetails'] = $disputesData;
                                        foreach($data['disputeDetails'] as $dispute){
                                            $issue = Mst_Issues::find($dispute->issue_id);
                                            $dispute->issue = $issue->issue;
                                            $customer = Trn_store_customer::find($dispute->customer_id);
                                            $dispute->customer_name = @$customer->customer_first_name." ".@$customer->customer_last_name;
                                            
                                            $issueType = Sys_IssueType::find($dispute->issue_id);

                                            if(isset($issueType->issue_type))
                                            $dispute->issue_type = @$issueType->issue_type;
                                            else
                                            $dispute->issue_type = '';
                                            
                                          
                                            
                                              if(isset($dispute->discription))
                                               $dispute->discription = $dispute->discription; 
                                               else
                                               $dispute->discription = ''; 
            
                                               
                                               if(isset($dispute->store_response))
                                               $dispute->store_response = $dispute->store_response; 
                                               else
                                               $dispute->store_response = '';
                                               
                                        }
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
                            
                        }
                        else
                        {
                            $data['status'] = 0;
                            $data['message'] = "failed";
                            $data['message'] = "Dispute not found ";
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

    public function viewDispute(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->dispute_id) && Mst_dispute::find($request->dispute_id))
                {
                   
                                    if($data['disputeDetails']  = Mst_dispute::where('dispute_id',$request->dispute_id)
                                    ->select("*")->first())
                                    {
                                            $issue = Mst_Issues::find($data['disputeDetails']->issue_id);
                                            $data['disputeDetails']->issue = $issue->issue;
                                            $issueType = Sys_IssueType::find($data['disputeDetails']->issue_id);

                                            if(isset($issueType->issue_type))
                                            $data['disputeDetails']->issue_type = @$issueType->issue_type;
                                            else
                                            $data['disputeDetails']->issue_type = '';

                                            $customer = Trn_store_customer::find($data['disputeDetails']->customer_id);
                                            $data['disputeDetails']->customer_name = @$customer->customer_first_name." ".@$customer->customer_last_name;
                                        
                                        
                                           if(isset($data['disputeDetails']->discription))
                                           $data['disputeDetails']->discription = $data['disputeDetails']->discription; 
                                           else
                                           $data['disputeDetails']->discription = ''; 
        
                                           
                                           if(isset($data['disputeDetails']->store_response))
                                           $data['disputeDetails']->store_response = $data['disputeDetails']->store_response; 
                                           else
                                           $data['disputeDetails']->store_response = ''; 
                                           
                                            
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
                    $data['message'] = "Dispute not found ";
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

    public function updateDispute(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->dispute_id) && $dispData = Mst_dispute::find($request->dispute_id))
                {
                    $dispute_id = $request->dispute_id;

                    $validator = Validator::make($request->all(),
                    [
                        'dispute_status'          => 'required',
                    ],
                    [
                        'dispute_status.required'        => 'Dispute status required',
                    ]);
                     
                        if(!$validator->fails() )
                        {
                            $disputeData['dispute_status'] = $request->dispute_status; // status
                            
                            //if(isset($request->discription))
                           // $disputeData['discription'] = $request->discription; // status
                           
                           
                            
                            if(isset($request->store_response))
                            $disputeData['store_response'] = $request->store_response; // store_response
                            
                            
                          
                                    
                            if(Mst_dispute::where('dispute_id',$request->dispute_id)->update($disputeData))
                            {
                                
                               if($request->dispute_status == 1)
                               {
                                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $dispData->customer_id)->get();
                                    $orderData = Trn_store_order::find($dispData->order_id);
    
                                        foreach ($customerDevice as $cd) {
                                            $title = 'Dispute closed';
                                            //  $body = 'First order points credited successully..';
                                            $body =  'Your dispute with order number'. $orderData->order_number . ' is closed by store..';
                                            $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body);
                                        }
                               }
                           
                               if($request->dispute_status == 3)
                               {
                                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $dispData->customer_id)->get();
                                    $orderData = Trn_store_order::find($dispData->order_id);
    
                                        foreach ($customerDevice as $cd) {
                                            $title = 'Dispute in progress';
                                            //  $body = 'First order points credited successully..';
                                            $body =  'Your dispute with order number'. $orderData->order_number . ' is in progress..';
                                            $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body);
                                        }
                               }
                               
                               if($request->dispute_status == 4)
                               {
                                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $dispData->customer_id)->get();
                                    $orderData = Trn_store_order::find($dispData->order_id);
    
                                        foreach ($customerDevice as $cd) {
                                            $title = 'Dispute is returned';
                                            //  $body = 'First order points credited successully..';
                                            $body =  'Your dispute with order number'. $orderData->order_number . ' is returned..';
                                            $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body);
                                        }
                               }
                            
                            
                            
                                $data['status'] = 1;
                                $data['message'] = "Dispute updated successfully.";
                                return response($data);
                            }
                            else
                            {
                                $data['status'] = 0;
                                $data['message'] = "failed.";
                                return response($data);
                            } 
                        }
                        else
                        {
                            $data['status'] = 0;
                            $data['message'] = "failed";
                            $data['message'] = "Dispute not found ";
                            return response($data);
                        }
                }
                else
                {
                    $data['status'] = 0;
                    $data['message'] = "Dispute not found ";
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
