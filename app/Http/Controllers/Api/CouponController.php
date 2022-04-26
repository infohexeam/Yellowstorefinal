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

use App\Models\admin\Mst_Coupon;

class CouponController extends Controller
{
    
    public function listCoupon(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                {
                    $store_id = $request->store_id;
                    if($data['couponDetails'] = Mst_Coupon::where('store_id',$store_id)->get())
                    {
                       
                        $data['status'] = 1;
                        $data['message'] = "success";
                        return response($data);
                    }
                    else
                    {
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

    public function listFilterCoupon(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                {
                    
                            $validator = Validator::make($request->all(),
                            [
                                'coupon_status'   => 'required',
                            ],
                            [
                                'coupon_status.required'  => 'Coupon status required',
                            ]);

                            if(!$validator->fails())
                            {
                                if(1)
                                {
                                    $couponDetail = Mst_Coupon::where('store_id',$request->store_id);

                                    $couponDetail = $couponDetail->where('coupon_status',$request->coupon_status);
 
                                    if($request->coupon_status == 0)
                                    {
                                        $today = Carbon::now()->toDateTimeString();

                                        $couponDetail = $couponDetail->whereDate('valid_from' ,'<=' ,$today)->whereDate('valid_to','>=',$today);
                                        // $couponDetail = $couponDetail->whereDate('valid_to' ,'>=' ,$today);
                                    }         
                                    
                                    $data['couponDetails'] = $couponDetail->orderBy('coupon_id','DESC')->get();
                                    $data['status'] = 1;
                                    $data['message'] = "success";
                                    return response($data);
                                }
                                else
                                {
                                    $data['status'] = 0;
                                    $data['message'] = "failed";
                                    return response($data);
                                }
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



    public function saveCoupon(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                {
                    
                    $validator = Validator::make($request->all(),
                    [
                        'coupon_code'          => 'required',
                        'coupon_type'          => 'required',
                        'discount_type'          => 'required',
                        'discount'          => 'required',
                        'valid_to'          => 'required',
                        'valid_from'          => 'required',
                        'coupon_status'          => 'required',
                    //    'min_purchase_amt'          => 'required',

                    ],
                    [
                        'coupon_code.required'             => 'Code required',
                        'coupon_type.required'             => 'Type required',
                        'discount.required'             => 'Discount required',
                        'discount_type.required'             => 'Discount type required',
                        'valid_to.required'             => 'Valid to required',
                        'valid_from.required'             => 'Valid from required',
                        'coupon_status.required'             => 'Status required',
                        'min_purchase_amt.required'             => 'Minimum purchase ampunt required',

                    ]);

                    if(!$validator->fails())
                    {
                        
                        $coupon = new Mst_Coupon();
                        $coupon->store_id = $request->store_id;
                        $coupon->coupon_code = $request->coupon_code;
                        $coupon->coupon_type = $request->coupon_type;
                        $coupon->discount_type = $request->discount_type;
                        $coupon->min_purchase_amt = $request->min_purchase_amt;
                        $coupon->discount = $request->discount;
                        $coupon->valid_to = $request->valid_to;
                        $coupon->valid_from = $request->valid_from;
                        $coupon->coupon_status = $request->coupon_status;
                        
                        if($coupon->save())
                        {
                            $data['status'] = 1;
                            $data['message'] = "Coupon saved";
                            return response($data);
                        }
                        else
                        {
                            $data['status'] = 0;
                            $data['message'] = "failed";
                            return response($data);
                        }
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


    public function listCouponType(Request $request)
    {
        $data = array(); 
        
        try { 
                
            $data['couponTypeDetails'] = [
                [
                    'coupon_type_id' => 1,
                    'coupon_type_name'=> "Single use"
                ],
                [
                    'coupon_type_id' => 2,
                    'coupon_type_name'=> "Multi use"
                ]
            ];

                    $data['status'] = 1;
                    $data['message'] = "success";
                    return response($data);
             
                
        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }
    }


    public function listDiscountType(Request $request)
    {
        $data = array(); 
        
        try { 
                
                
            $data['discountTypeDetails'] = [
                [
                    'discount_type_id' => 1,
                    'discount_type_name'=> "Fixed"
                ],
                [
                    'discount_type_id' => 2,
                    'discount_type_name'=> "Percentage"
                ]
            ];

                    $data['status'] = 1;
                    $data['message'] = "success";
                    return response($data);
             
                
        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }
    }

    

    public function editCoupon(Request $request)
    {
        $data = array(); 
        
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                {  
                    $validator = Validator::make($request->all(),
                    [
                        'coupon_id'          => 'required',
                    ],
                    [
                        'coupon_id.required'        => 'Coupon required',
                    ]);
                     
                        if(!$validator->fails() )
                        {
                            if($request->coupon_id == 0  ||   Mst_Coupon::find($request->coupon_id))
                            {
                                $coupon_id = $request->coupon_id;
                                $store_id = $request->store_id;
                                
                                if($data['couponDetails'] = Mst_Coupon::where('store_id',$store_id)
                                ->where('coupon_id',$coupon_id)->first())
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
                                $data['message'] = "failed";
                                $data['message'] = "Coupon not found ";
                                return response($data);
                            }
                        }
                        else
                        {
                            $data['status'] = 0;
                            $data['message'] = "failed";
                            $data['message'] = "Category not found ";
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


    public function updateCoupon(Request $request)
    {
        $data = array(); 
        
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                {  
                    
                            if($request->coupon_id == 0  ||   Mst_Coupon::find($request->coupon_id))
                            {
                                $coupon_id = $request->coupon_id;
                                $store_id = $request->store_id;
                                
                                $validator = Validator::make($request->all(),
                                [
                                    'coupon_code'          => 'required',
                                    'coupon_type'          => 'required',
                                    'discount_type'          => 'required',
                                    'discount'          => 'required',
                                    'valid_to'          => 'required',
                                    'valid_from'          => 'required',
                                    'coupon_status'          => 'required',
                                //    'min_purchase_amt'          => 'required',

                                ],
                                [
                                    'coupon_code.required'             => 'Code required',
                                    'coupon_type.required'             => 'Type required',
                                    'discount.required'             => 'Discount required',
                                    'discount_type.required'             => 'Discount type required',
                                    'valid_to.required'             => 'Valid to required',
                                    'valid_from.required'             => 'Valid from required',
                                    'coupon_status.required'             => 'Status required',
                                    'min_purchase_amt.required'             => 'Minimum purchase ampunt required',

                                ]);

                                if(!$validator->fails())
                                {
                                    
                                    $coupon['coupon_code'] = $request->coupon_code;
                                    $coupon['coupon_type'] = $request->coupon_type;
                                    $coupon['discount_type'] = $request->discount_type;
                                    $coupon['discount'] = $request->discount;
                                    $coupon['valid_to'] = $request->valid_to;
                                    $coupon['valid_from'] = $request->valid_from;
                                    $coupon['coupon_status'] = $request->coupon_status;
                                    $coupon['min_purchase_amt'] = $request->min_purchase_amt;

                                    if(Mst_Coupon::where('coupon_id',$coupon_id)->where('store_id',$store_id)->update($coupon))
                                    {
                                        $data['status'] = 1;
                                        $data['message'] = "Coupon updated";
                                        return response($data);
                                    }
                                    else
                                    {
                                        $data['status'] = 0;
                                        $data['message'] = "failed";
                                        return response($data);
                                    }
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
                                $data['message'] = "failed";
                                $data['message'] = "Coupon not found ";
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


    public function deleteCoupon(Request $request)
    {
        $data = array(); 
        
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                {  
                    
                            if($request->coupon_id == 0  ||   Mst_Coupon::find($request->coupon_id))
                            {
                                $coupon_id = $request->coupon_id;
                                $store_id = $request->store_id;
                               
                                    if(Mst_Coupon::where('coupon_id',$coupon_id)->where('store_id',$store_id)->delete())
                                    {
                                        $data['status'] = 1;
                                        $data['message'] = "Coupon deleted";
                                        return response($data);
                                    }
                                    else
                                    {
                                        $data['status'] = 0;
                                        $data['message'] = "failed";
                                        return response($data);
                                    }
                                
                                
                                
                            }
                            else
                            {
                                $data['status'] = 0;
                                $data['message'] = "failed";
                                $data['message'] = "Coupon not found ";
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
