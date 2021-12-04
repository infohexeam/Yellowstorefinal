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
                   
                                    if($data['disputeDetails']  = \DB::table("mst_disputes")
                                    ->where('dispute_id',$request->dispute_id)
                                    ->select("*")->first())
                                    {
                                            $issue = Mst_Issues::find($data['disputeDetails']->issue_id);
                                            $data['disputeDetails']->issue = $issue->issue;
                                            $customer = Trn_store_customer::find($data['disputeDetails']->customer_id);
                                            $data['disputeDetails']->customer_name = @$customer->customer_first_name." ".@$customer->customer_last_name;
                                        
                                        
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
                if(isset($request->dispute_id) && Mst_dispute::find($request->dispute_id))
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
                            
                            if(isset($request->discription))
                            $disputeData['discription'] = $request->discription; // status
                          
                                    
                            if(Mst_dispute::where('dispute_id',$request->dispute_id)->update($disputeData))
                            {
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
