<?php

namespace App\Http\Controllers\Customer_Api;

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
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Trn_ProductVariantAttribute;

use App\Models\admin\Mst_product_image;
use App\Models\admin\Trn_GlobalProductImage;

use App\Models\admin\Mst_CustomerAppBanner;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Trn_Cart;
use App\Models\admin\Mst_Coupon;
use App\Models\admin\Mst_dispute;
use App\Models\admin\Mst_Issues;
use App\Models\admin\Sys_IssueType;
use App\Models\admin\Trn_StoreDeliveryTimeSlot;
use App\Models\admin\Sys_payment_type;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_order_invoice;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Mst_delivery_boy;

use App\Models\admin\Trn_StoreVisitByCustomer;
use App\Models\admin\Trn_CategoryVisitByCustomer;
use App\Models\admin\Trn_RecentlyVisitedProducts;


class VisitController extends Controller
{
    public function storeVisitByCustomer(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->store_id) && Mst_store::find($request->store_id))
                {
                    if(isset($request->customer_id) && Trn_store_customer::find($request->customer_id))
                    {
                        $store_id = $request->store_id;
                        $customer_id = $request->customer_id;
                        if($data['storeVisitCount']  = Trn_StoreVisitByCustomer::
                            where('store_id',$store_id)
                            ->where('customer_id',$customer_id)
                            ->sum('visit_count'))
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
                        $data['message'] = "Customer not found";
                        return response($data);
                    }
                }
                else
                {
                    $data['status'] = 4;
                    $data['message'] = "Store not found";
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


    public function businessTypeVisitByCustomer(Request $request)
    {
        $data = array(); 
        try {
                if(isset($request->business_type_id) && Mst_business_types::find($request->business_type_id))
                {
                    if(isset($request->customer_id) && Trn_store_customer::find($request->customer_id))
                    {
                        $business_type_id = $request->business_type_id;
                        $customer_id = $request->customer_id;
                        if($businessTypeVisitCount  = Trn_StoreVisitByCustomer::
                            join('mst_stores','mst_stores.store_id','=','trn__store_visit_by_customers.store_id')
                            ->where('mst_stores.business_type_id',$business_type_id)
                            ->where('trn__store_visit_by_customers.customer_id',$customer_id)
                            ->sum('trn__store_visit_by_customers.visit_count'))
                        {
                            $data['businessTypeVisitCount'] = $businessTypeVisitCount;
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
                        $data['message'] = "Customer not found";
                        return response($data);
                    }
                }
                else
                {
                    $data['status'] = 4;
                    $data['message'] = "Business type not found";
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
    
     public function productRemoved(Request $request)
    {
        $data = array(); 
        try {
                    if(isset($request->customer_id) && Trn_store_customer::find($request->customer_id))
                    {
                        $customer_id = $request->customer_id;
                       
                        if($data['productsRemoved']  = Trn_Cart::where('remove_status',1)->get())
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
                        $data['message'] = "Customer not found";
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
    
      public function categoryVisitByCustomer(Request $request)
    {
        $data = array(); 
        try {
                    if(isset($request->customer_id) && Trn_store_customer::find($request->customer_id))
                    {
                        $customer_id = $request->customer_id;
                        $store_id = $request->store_id;
                        $category_id = $request->category_id;

                        if($categoryVisitCount  = Trn_CategoryVisitByCustomer::
                            where('category',$category_id)
                            ->where('store_id',$store_id)
                            ->where('customer_id',$customer_id)
                            ->count())
                        {
                            $data['categoryVisitCount'] = $categoryVisitCount;
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
                        $data['message'] = "Customer not found";
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
    
    
     public function productsVisited(Request $request)
    {
        $data = array(); 
        try {
               
                    if(isset($request->customer_id) && Trn_store_customer::find($request->customer_id))
                    { 
                        $business_type_id = $request->business_type_id;
                        $customer_id = $request->customer_id;
                        
                        if(
                            $data['productsVisited']  = Trn_RecentlyVisitedProducts::
                            join('mst_store_products','mst_store_products.product_id','=','trn__recently_visited_products.product_id')
                            ->join('mst_store_product_varients','mst_store_product_varients.product_varient_id','=','trn__recently_visited_products.product_varient_id')
                             ->join('mst_stores','mst_stores.store_id','=','trn__recently_visited_products.store_id')
                            ->select(
                                'mst_store_products.product_id',
                                'mst_store_products.product_name',
                                'mst_store_products.product_code',
                                'mst_store_products.product_base_image',
                                'mst_store_products.show_in_home_screen',
                                'mst_store_products.product_status',
                                'mst_store_product_varients.product_varient_id',
                                'mst_store_product_varients.variant_name',
                                'mst_store_product_varients.product_varient_price',
                                'mst_store_product_varients.product_varient_offer_price',
                                'mst_store_product_varients.product_varient_base_image',
                                'mst_store_product_varients.stock_count',
                                'mst_store_products.store_id',
                                'mst_stores.business_type_id'
                                )
                            ->where('trn__recently_visited_products.customer_id',$customer_id)
                            ->where('mst_store_products.product_status',1)
                            ->get()
                            )
                        {

                                foreach($data['productsVisited'] as $product){
                                    $product->product_base_image = '/assets/uploads/products/base_product/base_image/'.$product->product_base_image;
                                    $product->product_varient_base_image = '/assets/uploads/products/base_product/base_image/'.$product->product_varient_base_image;
                                    
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
                        $data['status'] = 0;
                        $data['message'] = "Customer not found ";
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
