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
use Illuminate\Support\Facades\Auth;
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
use App\Models\admin\Trn_CustomerDeviceToken;
use App\Models\admin\Trn_StoreDeviceToken;
use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Trn_StoreWebToken;
use App\Models\admin\Trn_customer_reward;

use App\Models\admin\Mst_StockDetail;
use App\Models\admin\Trn_customerAddress;
use App\Models\admin\Trn_DeliveryBoyLocation;


use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Trn_OrderSplitPayments;
use App\Trn_wallet_log;

class StoreOrderController extends Controller
{


    public function saveOrderService(Request $request)
    {
        //dd($request->all());

        try {




            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'customer_id'   => 'required',
                        'order_total_amount'  => 'required',
                        'payment_type_id'   => 'required',
                        'status_id' => 'required',
                        'product_variants.*.product_id'    => 'required',
                        'product_variants.*.product_varient_id'    => 'required',
                        'product_variants.*.quantity'    => 'required',
                        'product_variants.*.unit_price'    => 'required',
                        'product_variants.*.total_amount'    => 'required',
                        'product_variants.*.tax_amount'    => 'required',
                        'product_variants.*.discount_amount'    => 'required',                       
                    ],
                    [
                        'customer_id.required'  => 'Customer required',
                        'total_amount.required' => 'Total order amount required',
                        'payment_type_id.required'  => 'Payment type required',
                        'status_id.required'    => 'Status required',
                        'product_variants.*.product_id.required'    => 'Product required',
                        'product_variants.*.product_varient_id.required'    => 'Product variant required',
                        'product_variants.*.quantity.required'    => 'Product quantity required',
                        'product_variants.*.unit_price.required'    => 'Product quantity required',
                        'product_variants.*.total_amount.required'    => 'Total amount required',
                        'product_variants.*.tax_amount.required'    => 'Tax amount required',
                        'product_variants.*.discount_amount.required'    => 'Discount amount required',
                        // 'product_variants.*.discount_percentage.required'    =>'Discount percentage required',
                    ]
                );

                if (!$validator->fails()) {
                    $cust=Trn_store_customer::where('customer_id',$request->customer_id)->first();
                    if($cust)
                    {
                        if($cust->customer_profile_status==0)
                        {
                            $data['status'] = 0;
                            $data['message'] = "Profile is not active";
                            return response($data);
                        }

                    }
                    $noStockProducts = array();
                    $varProdu=Mst_store_product_varient::find($request->product_varient_id);
                    $proData = Mst_store_product::find($varProdu->product_id);
                    //return $proData;
                    if($proData->is_timeslot_based_product=="1")
                    {
                        $currentTime = now();
                        $prdt=Mst_store_product::where('product_id', $varProdu->product_id)->first();
                        // $proDataFetch= Mst_store_product::where('product_id', $varProdu->product_id)
                        //         ->where('timeslot_start_time', '<=', $currentTime)
                        //         ->where('timeslot_end_time', '>=', $currentTime)
                        //         ->exists();
                        // if(!$proDataFetch)
                        // {
                        //     $start=$prdt->timeslot_start_time;
                        //     $end=$prdt->timeslot_end_time;
                        //     $data['status'] = 3;
                        //     $data['message'] = "Product Unavailable. The product will be available from '.date('g:i A',strtotime($start)) .' to '.date('g:i A',strtotime($end))";
                        //     return response($data);

                        // }

                    }
                    $start = $proData->timeslot_start_time; //init the start time
                    $end = $proData->timeslot_end_time; //init the end time
                            //return $start;
                    $slot=[];
                    if($request->time_slot)
                    {
                        $slot=Trn_StoreDeliveryTimeSlot::find($request->time_slot);
                    }
                    $pua=0;
                    if($request->time_slot==0)
                    {
                        $currTime = date("G:i");
                                
                        //return $start;
                        if($proData->is_timeslot_based_product==1)
                        {
                               
                            if ($currTime<$start || $currTime>$end)
                                {
                                    $pua=$pua+1;
                            
                                }
                                   
                            
                        }
                        //return $pua;


                    }
                           
                            if ($proData->is_timeslot_based_product==1)
                            {
                                if($slot)
                                {
                                    if($slot->time_start<$start || $slot->time_end>$end)
                                    {
                                        $pua=$pua+1;
                                    }
                                }
                           
                            }
                            
                           
                            if($pua>0)
                            {
                                $start=$prdt->timeslot_start_time;
                                $end=$prdt->timeslot_end_time;
                                $data['status'] = 3;
                                $data['message'] = 'Product Unavailable. The product will be available from '.date('g:i A',strtotime($start)) .' to '.date('g:i A',strtotime($end));
                                return response($data);
    
                            }
                       
        
                    foreach ($request->product_variants as $value) {
                        $varProdu = Mst_store_product_varient::find($value['product_varient_id']);
                        $proData = Mst_store_product::find($varProdu->product_id);
                        if (isset($varProdu)) {
                            if ($value['quantity'] > $varProdu->stock_count) {
                                if (@$proData->product_name != $varProdu->variant_name) {
                                    $data['product_name'] = @$proData->product_name . " " . $varProdu->variant_name;
                                } else {
                                    $data['product_name'] = @$proData->product_name;
                                }

                                $noStockProducts[] = $varProdu->product_varient_id;

                                $data['product_varient_id'] = $varProdu->product_varient_id;
                                $data['product_id'] = $varProdu->product_id;
                                $data['message'] = 'Stock unavailable';
                                $data['status'] = 2;
                                //  return response($data);
                            }
                        } else {
                            $data['message'] = 'Product not found';
                            $data['status'] = 2;
                            return response($data);
                        }
                    }
                    if (count($noStockProducts) > 0) {
                        $data['noStockProducts'] = $noStockProducts;
                        return response($data);
                    }
                    $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();

                    $orderNumber = @$storeOrderCount + 1;

                    $store_data = Mst_store::find($request->store_id);

                    if (isset($store_data->order_number_prefix)) {
                        $orderNumberPrefix = $store_data->order_number_prefix;
                    } else {
                        $orderNumberPrefix = 'ORDRYSTR';
                    }
                    $productVarOlddata = Mst_store_product_varient::find($request->product_varient_id);
                    $proDataZ = Mst_store_product::find($productVarOlddata->product_id);
    
                    $taxData = Mst_Tax::find($proDataZ->tax_id);
                    $iTax = @$productVarOlddata->product_varient_offer_price * @$taxData->tax_value / (100 + @$taxData->tax_value);
                    $iDis = @$productVarOlddata->product_varient_price - @$productVarOlddata->product_varient_offer_price;
                    $store_order = new Trn_store_order;

                    $store_order->service_order =  1;
                    $store_order->service_booking_order =  $request->service_booking_order;
                    $store_order->product_varient_id =  $request->product_varient_id;

                    $store_order->order_number =$orderNumberPrefix .substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4). @$orderNumber; //$orderNumberPrefix . @$orderNumber;
                    // $store_order->order_number = 'ORDRYSTR'.@$orderNumber;
                    $store_order->customer_id = $request->customer_id;
                    $store_order->store_id =  $request->store_id;


                    $store_order->subadmin_id =  $store_data->subadmin_id;
                    $store_order->product_total_amount =  $request->order_total_amount - $request->amount_reduced_by_rp;
                    $store_order->payment_status = 1;
                    $store_order->status_id = 1;

                    // online
                    $store_order->payment_type_id = $request->payment_type_id;
                    $store_order->delivery_charge =  $request->delivery_charge;
                    $store_order->packing_charge =  $request->packing_charge;

                    $store_order->time_slot =  $request->time_slot;
                    $store_order->delivery_option =  $request->delivery_option??1;



                    $store_order->delivery_address =  $request->delivery_address;

                    $store_order->coupon_id =  $request->coupon_id;
                    $store_order->coupon_code =  $request->coupon_code;
                    $store_order->reward_points_used =  $request->reward_points_used;
                    $store_order->amount_before_applying_rp =  $request->amount_before_applying_rp;
                    $store_order->amount_reduced_by_rp =  $request->amount_reduced_by_rp;
                    $store_order->order_type = 'APP';

                    if (isset($request->amount_reduced_by_coupon))
                        $store_order->amount_reduced_by_coupon =  $request->amount_reduced_by_coupon;
                    $store_order->service_tax_id=$proDataZ->tax_id;
                    $store_order->service_tax_value=$taxData->tax_value;
                    $store_order->service_tax_amount=$iTax;
                    $store_order->service_discount_amount=$iDis;
                    $store_order->service_mrp=$productVarOlddata->product_varient_price;
                    
                    $store_order->save();
                    $order_id = DB::getPdo()->lastInsertId();

                    // if(Trn_store_order::where('customer_id',$request->customer_id)->count() < 1)
                    // {
                    //      $configPoint = Trn_configure_points::find(1);

                    //     $cr = new Trn_customer_reward;
                    //     $cr->transaction_type_id = 0;
                    //     $cr->reward_points_earned = $configPoint->first_order_points;
                    //     $cr->customer_id = $request->customer_id;
                    //     $cr->order_id = $order_id;
                    //     $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
                    //     $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                    //     $cr->reward_point_status = 1;
                    //     $cr->discription = "First order points";
                    //     $cr->save();
                    // }


                    $invoice_info['order_id'] = $order_id;
                    $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
                    $invoice_info['invoice_id'] = "INV0" . $order_id;
                    $invoice_info['created_at'] = Carbon::now();
                    $invoice_info['updated_at'] = Carbon::now();

                    Trn_order_invoice::insert($invoice_info);

                    foreach ($request->product_variants as $value) {
                        $productVarOlddata = Mst_store_product_varient::find($value['product_varient_id']);


                        if (!isset($value['discount_amount'])) {
                            $value['discount_amount'] = 0;
                        }



                        $total_amount = $value['quantity'] * $value['unit_price'];

                        $data2 = [
                            'order_id' => $order_id,
                            'product_id' => $value['product_id'],
                            'product_varient_id' => $value['product_varient_id'],
                            'customer_id' => $request['customer_id'],
                            'store_id' => $request['store_id'],
                            'quantity' => $value['quantity'],
                            'unit_price' =>  $value['unit_price'],
                            'tax_amount' => $value['tax_amount'],
                            'mrp'=>$productVarOlddata->product_varient_price,
                            
                            'total_amount' => $total_amount,
                            'discount_amount' => $value['discount_amount'],
                            //'discount_percentage'=> $value['discount_percentage'],
                            'created_at'         => Carbon::now(),
                            'updated_at'         => Carbon::now(),
                        ];
                        Trn_store_order_item::insert($data2);
                    }



                    $storeDatas = Trn_StoreAdmin::where('store_id', $request->store_id)->where('role_id', 0)->first();
                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $request->customer_id)->get();
                    $storeDevice = Trn_StoreDeviceToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $request->store_id)->get();
                    $orderdatas = Trn_store_order::find($order_id);

                    foreach ($storeDevice as $sd) {
                        $title = 'New service order arrived';
                        $body = 'New order with order id ' . $orderdatas->order_number . ' has been saved successully..';
                        $clickAction = "OrdersFragment";
                        $type = "order";
                        $data['response'] =  $this->storeNotification($sd->store_device_token, $title, $body,$clickAction,$type);
                    }


                    $storeWeb = Trn_StoreWebToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $request->store_id)->get();
                    foreach ($storeWeb as $sw) {
                        $title = 'New service order arrived';
                        $body = 'New order with order id ' . $orderdatas->order_number . ' has been saved successully..';
                        $clickAction = "OrderListFragment";
                        $type = "order";
                        $data['response'] =  Helper::storeNotifyWeb($sw->store_web_token, $title, $body,$clickAction,$type);
                    }

                    foreach ($customerDevice as $cd) {
                        $title = 'Order Placed';
                        $body = 'Your order with order id ' . $orderdatas->order_number . ' has been saved successully..';
                        
                        $clickAction = "OrderListFragment";
                        $type = "order";
                        //   $title = 'Title';
                        //  $body = 'Body';

                        $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                    }



                    $data['status'] = 1;
                    $data['order_id'] = $order_id;
                    if ($request->service_booking_order == 1) {
                        $data['message'] = "Booking successful.";
                    } else {
                        $data['message'] = "Order saved.";
                    }

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

    public function storeTimeSlots(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                if ($data['timeSlotDetails']  = Trn_StoreDeliveryTimeSlot::select('store_delivery_time_slot_id', 'store_id', 'time_start', 'time_end')
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
                $data['status'] = 4;
                $data['message'] = "Customer not found";
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


    public function listPaymentType(Request $request)
    {
        $data = array();
        try {

            if ($data['paymentTypes']  = Sys_payment_type::all()) {

                $data['status'] = 1;
                $data['message'] = "success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "failed";
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

    public function pgTest(Request $request)
    {
        $client = new \GuzzleHttp\Client();

        $order_ID = intval($request->orderId);

        $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/' . $request->orderId, [
            'headers' => [
                'Accept' => 'application/json',
                'x-api-version' => '2021-05-21',
                'x-client-id' => '165253d13ce80549d879dba25b352561',
                'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
            ],
        ]);
        return  $responseData = $response->getBody()->getContents();
    }

    
    public function saveOrderLock(Request $request)
    {
        
        try {

            if (isset($request->store_id) && $orderStoreData = Mst_store::find($request->store_id)) {
                $isActiveSlot=Helper::findHoliday($request->store_id);
                if($isActiveSlot==false)
                {
                    $data['status'] = 0;
                    $data['message'] = "You cannot place an order now.store closed";
                    return response($data);

                }
                $getParentExpiry = Trn_StoreAdmin::where('store_id','=',$request->store_id)->where('role_id','=',0)->first();
                if($getParentExpiry)
                {
                    $today = Carbon::now()->toDateString();
                    $parentExpiryDate = $getParentExpiry->expiry_date;
                    if($today>$parentExpiryDate)
                    {
                            
                        $data['status'] = 0;
                        $data['message'] = 'Store was not avaliable from '.date('d-M-Y',strtotime($parentExpiryDate)).' You cannot place an order';
                        return response($data);          
                    }
                    
    
                }
                $validator = Validator::make(
                    $request->all(),
                    [
                        'customer_id'   => 'required',
                        'order_total_amount'  => 'required',
                        'payment_type_id'   => 'required',
                        'status_id' => 'required',
                        'product_variants.*.cart_id'    => 'required',
                        'product_variants.*.product_id'    => 'required',
                        'product_variants.*.product_varient_id'    => 'required',
                        'product_variants.*.quantity'    => 'required',
                        'product_variants.*.unit_price'    => 'required',
                        'product_variants.*.total_amount'    => 'required',
                        'product_variants.*.tax_amount'    => 'required',
                        'product_variants.*.discount_amount'    => 'required',
                        //  'product_variants.*.discount_percentage'    =>'required',
                    ],
                    [
                        'customer_id.required'  => 'Customer required',
                        'total_amount.required' => 'Total order amount required',
                        'payment_type_id.required'  => 'Payment type required',
                        'status_id.required'    => 'Status required',
                        'product_variants.*.cart_id.required'    => 'Cart ID required',
                        'product_variants.*.product_id.required'    => 'Product required',
                        'product_variants.*.product_varient_id.required'    => 'Product variant required',
                        'product_variants.*.quantity.required'    => 'Product quantity required',
                        'product_variants.*.unit_price.required'    => 'Product quantity required',
                        'product_variants.*.total_amount.required'    => 'Total amount required',
                        'product_variants.*.tax_amount.required'    => 'Tax amount required',
                        'product_variants.*.discount_amount.required'    => 'Discount amount required',
                        // 'product_variants.*.discount_percentage.required'    =>'Discount percentage required',
                    ]
                );

                if (!$validator->fails()) {
                    $cust=Trn_store_customer::where('customer_id',$request->customer_id)->first();
                    if($cust)
                    {
                        if($cust->customer_profile_status==0)
                        {
                            $data['status'] = 0;
                            $data['message'] = "Profile is not active";
                            return response($data);
                        }

                    }
                    $noStockProducts = array();
                    foreach ($request->product_variants as $value) {
                        $varProdu = Mst_store_product_varient::find($value['product_varient_id']);
                        $proData = Mst_store_product::find($varProdu->product_id);
                        if($varProdu )
                        {
                            $stockDiffernece=$varProdu->stock_count-$value['quantity'];
                            if($stockDiffernece<0)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Some products quantity is more than available stock..Try again";
                                DB::rollback();
                                return response($data);

                            }
                        }
                    }


                    if (count($noStockProducts) > 0) {
                        $data['noStockProducts'] = $noStockProducts;
                        return response($data);
                    }
                    // $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();

                    // $orderNumber = @$storeOrderCount + 1;

                    $store_data = Mst_store::find($request->store_id);

                    if (isset($store_data->order_number_prefix)) {
                        $orderNumberPrefix = $store_data->order_number_prefix;
                    } else {
                        $orderNumberPrefix = 'ORDRYSTR';
                    }
                    // $last_order_number=Helper::checkOrderNumber($request->store_id);
                    // $orderNumber = $last_order_number + 1;
                    // $order_no_exists=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                    // if($order_no_exists)
                    // {
                    //   $orderNumber=$orderNumber+1;
                    // }
                    $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();

                    $orderNumber = @$storeOrderCount + 1;


                    $store_order = new Trn_store_order;

                    if (isset($request->service_order))
                        $store_order->service_order =  $request->service_order; // service order - booking order


                    $store_order->order_number = $orderNumberPrefix .substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4). @$orderNumber;
                    // $store_order->order_number = 'ORDRYSTR'.@$orderNumber;
                    $store_order->customer_id = $request->customer_id;
                    $store_order->store_id =  $request->store_id;


                    $store_order->subadmin_id =  $store_data->subadmin_id;
                    $store_order->product_total_amount =  $request->order_total_amount;
                    $store_order->payment_status = 1;
                    $store_order->status_id = $request->status_id;

                    // online
                    $store_order->payment_type_id = $request->payment_type_id;
                    $store_order->delivery_charge =  $request->delivery_charge;
                    $store_order->packing_charge =  $request->packing_charge;

                    $store_order->time_slot =  $request->time_slot;
                    $store_order->delivery_option =  $request->delivery_option??1;



                    $store_order->delivery_address =  $request->delivery_address;

                    $store_order->coupon_id =  $request->coupon_id;
                    $store_order->coupon_code =  $request->coupon_code;

                    if ($request->status_id != 5) {
                        $store_order->reward_points_used =  $request->reward_points_used;
                        $store_order->reward_points_used_store =  $request->reward_points_used_store;
                        $store_order->amount_before_applying_rp =  $request->amount_before_applying_rp;
                        $store_order->amount_reduced_by_rp =  $request->amount_reduced_by_rp;
                        $store_order->amount_reduced_by_rp_store =  $request->amount_reduced_by_rp_store;
                    } else {
                        $store_order->reward_points_used =  0;
                        $store_order->reward_points_used_store = 0;
                        $store_order->amount_before_applying_rp =  0;
                        $store_order->amount_reduced_by_rp =  0;
                        $store_order->amount_reduced_by_rp_store =  0;
                    }


                    $store_order->order_type = 'APP';
                    $store_order->trn_id = $request->orderId;

                    if ($request->payment_type_id == 2) {

                        if (Helper::isBankDataFilled($request->store_id) == 1) {
                            $store_order->is_split_data_saved = 0;
                        } else {
                            $store_order->is_split_data_saved = 1;
                        }
                    }

                    // $store_order->referenceId = $request->referenceId;
                    // $store_order->txTime = $request->txTime;
                    // $store_order->orderAmount = $request->orderAmount;
                    // $store_order->txMsg = $request->txMsg;
                    // $store_order->txStatus = $request->txStatus;


                    if (isset($request->amount_reduced_by_coupon))
                        $store_order->amount_reduced_by_coupon =  $request->amount_reduced_by_coupon;
                    $store_order->is_locked = 1; //transaction is locked
                    $store_order->save();
                    $order_id = DB::getPdo()->lastInsertId();

                    //delete cart items
                    Trn_Cart::where('customer_id', $request->customer_id)
                            ->update(['remove_status' =>  1]); //deleted


                    // $invoice_info['order_id'] = $order_id;
                    // $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
                    // $invoice_info['invoice_id'] = "INV0" . $order_id;
                    // $invoice_info['created_at'] = Carbon::now();
                    // $invoice_info['updated_at'] = Carbon::now();

                    // Trn_order_invoice::insert($invoice_info);

                        //add products to order item table
                    foreach ($request->product_variants as $value) {
                        $productVarOlddata = Mst_store_product_varient::find($value['product_varient_id']);
                       
                        if ($proData->service_type != 2) {
                         if($productVarOlddata)
                        {
                            $stockDiffernece=$productVarOlddata->stock_count-$value['quantity'];
                            if($stockDiffernece<0)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Some products quantity is more than available stock..Try again";
                                DB::rollback();
                                return response($data);

                            }
                        }

                        $prv=Mst_store_product_varient::where('product_varient_id', '=', $value['product_varient_id'])->first();
                        if($prv->stock_count<0)
                        {
                            Mst_store_product_varient::where('product_varient_id', '=', $value['product_varient_id'])->increment('stock_count', $value['quantity']);
                            $data['status'] = 0;
                            $data['message'] = "Some products quantity is more than available stock..Try again Later!!!!!!!!!1";
                            DB::rollback();
                            return response($data);

                        }

                            
                        }

                        if (!isset($value['discount_amount'])) {
                            $value['discount_amount'] = 0;
                        }

                        if ($proData->service_type != 2) {
                            $negStock = -1 * abs($value['quantity']);

                            $sd = new Mst_StockDetail;
                            $sd->store_id = $request->store_id;
                            $sd->product_id = $value['product_id'];
                            $sd->stock = $negStock;
                            $sd->product_varient_id = $value['product_varient_id'];
                            $sd->prev_stock = $productVarOlddata->stock_count;
                            $sd->save();
                        }

                        $proDataZ = Mst_store_product::find($productVarOlddata->product_id);

                        $taxData = Mst_Tax::find($proDataZ->tax_id);

                        $total_amount = $value['quantity'] * $value['unit_price'];

                        // $iTax = @$productVarOlddata->product_varient_offer_price * 100 / (100 + @$taxData->tax_value);
                        $iTax = @$productVarOlddata->product_varient_offer_price * @$taxData->tax_value / (100 + @$taxData->tax_value);
                        $iDis = @$productVarOlddata->product_varient_price - @$productVarOlddata->product_varient_offer_price;

                        $data2 = [
                            'order_id' => $order_id,
                            'cart_id' => $value['cart_id'],
                            'product_id' => $value['product_id'],
                            'product_varient_id' => $value['product_varient_id'],
                            'customer_id' => $request['customer_id'],
                            'store_id' => $request['store_id'],
                            'quantity' => $value['quantity'],
                             'product_name'=>$productVarOlddata->variant_name,
                                'product_image'=>$productVarOlddata->product_varient_base_image,
                            'mrp'=>@$productVarOlddata->product_varient_price,
                            'unit_price' =>  $value['unit_price'],
                            'tax_amount' => $iTax,
                            'tax_value'=>@$taxData->tax_value,
                            'tax_id'=>$proDataZ->tax_id,
                            'total_amount' => $total_amount,
                            'discount_amount' => $iDis,
                            'created_at'         => Carbon::now(),
                            'updated_at'         => Carbon::now(),
                        ];
                        Trn_store_order_item::insert($data2);
                    }

                    $orderdatas = Trn_store_order::find($order_id)->delete();
                    $getlockedOrder = Trn_store_order::withTrashed()->find($order_id);


                    $data['status'] = 1;
                    $data['lock_order_id'] = $getlockedOrder->order_id;
                    $data['message'] = "Order Locked.";
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

    public function releaseLock(Request $request)
    {
        try {
        if (isset($request->store_id) && $orderStoreData = Mst_store::find($request->store_id)) {

            if (isset($request->lock_order_id) && $orderStoreData = Trn_store_order::withTrashed()->find($request->lock_order_id)) {

                //get locked items and quantity from item table
                $getItems = Trn_store_order_item::where('order_id','=',$request->lock_order_id)->get();
                foreach($getItems as $items)
                {
                    $cartId = $items->cart_id;
                    $proVarient = $items->product_varient_id;
                    $proId = $items->product_id;
                    $orderQty = $items->quantity;
                    //restock in product stock in varient table 
                    Mst_store_product_varient::where('product_varient_id', '=', $proVarient)->increment('stock_count', $orderQty);
                    $proData = Mst_store_product::find($proId);
                    //restock stock table
                    $productVarOlddata = Mst_store_product_varient::find($proVarient);
                    
                    if ($proData->service_type != 2) {
                        /*if($productVarOlddata)
                        {
                            $stockDiffernece=$productVarOlddata->stock_count-$orderQty;
                            if($stockDiffernece<0)
                            {
                              continue;

                            }
                        }*/
                        $negStock = -1 * abs($orderQty);

                        $sd = new Mst_StockDetail;
                        $sd->store_id = $request->store_id;
                        $sd->product_id = $proId;
                        $sd->stock = $negStock;
                        $sd->product_varient_id = $proVarient;
                        $sd->prev_stock = $productVarOlddata->stock_count;
                        $sd->save();
                    }
                    //restore cart items from cart table 
                    Trn_Cart::where('cart_id', $cartId)
                    ->update(['remove_status' =>  0]);
                }
                //delete data from item table
                $getItems = Trn_store_order_item::where('order_id','=',$request->lock_order_id)->delete();
                //delete locked order
                Trn_store_order::withTrashed()->find($request->lock_order_id)->delete();

                $data['status'] = 1;
                $data['message'] = "Order deleted and Item Restocked";
                return response($data);


            } else {
                $data['status'] = 0;
                $data['message'] = "Locked Order not found ";
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


    public function saveOrder(Request $request)
    {
        //dd($request->all());

        try {
            if(isset($request->store_id))
            {
                DB::beginTransaction();

            $isActiveSlot=Helper::findHoliday($request->store_id);
                if($isActiveSlot==false)
                {
                    $data['status'] = 0;
                    $data['message'] = "You cannot place an order now.store closed";
                    return response($data);

                }
                $getParentExpiry = Trn_StoreAdmin::where('store_id','=',$request->store_id)->where('role_id','=',0)->first();
                $store=Mst_store::find($request->store_id);
                if($store->online_status==0)
                {
                    $data['status'] = 0;
                    $data['message'] = 'Store is offline now.Not possible to place an order.Try again later!';
                    return response($data);     

                }
                if($getParentExpiry)
                {
                    $today = Carbon::now()->toDateString();
                    $parentExpiryDate = $getParentExpiry->expiry_date;
                    if($today>$parentExpiryDate)
                    {
                            
                         $data['status'] = 0;
                        $data['message'] = 'Store was not avaliable from '.date('d-M-Y',strtotime($parentExpiryDate)).' You cannot place an order';
                        return response($data);          
                    }
                    if($getParentExpiry->store_account_status==0)
                    {
                        $data['status'] = 0;
                        $data['message'] = 'Store is inactive.Not possible to place an order!';
                        return response($data);       
                                    
                    }
                    
    
                }
            
           }

            if ($request->payment_type_id == 2) { //online
                //  $client = new \GuzzleHttp\Client();

                // $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/16817139', [
                //     'headers' => [
                //       'Accept' => 'application/json',
                //       'x-api-version' => '2021-05-21',
                //       'x-client-id' => '165253d13ce80549d879dba25b352561',
                //       'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
                //     ],
                //   ]);

                // $order_ID = intval($request->orderId);

                // $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/' . $order_ID, [
                //     'headers' => [
                //         'Accept' => 'application/json',
                //         'x-api-version' => '2021-05-21',
                //         'x-client-id' => '165253d13ce80549d879dba25b352561',
                //         'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
                //     ],
                // ]);

                // 'x-client-id' => '1159124beeb38480c16b093237219511',
                // 'x-client-secret' => 'f4201506d616394eebf87fa82e0b12385cd6c730'

                //   $responseData = $response->getBody()->getContents();
            }


           
                if (isset($request->store_id) && $orderStoreData = Mst_store::find($request->store_id)) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'customer_id'   => 'required',
                            'order_total_amount'  => 'required',
                            'payment_type_id'   => 'required',
                            'status_id' => 'required',
                            'product_variants.*.cart_id'    => 'required',
                            'product_variants.*.product_id'    => 'required',
                            'product_variants.*.product_varient_id'    => 'required',
                            'product_variants.*.quantity'    => 'required',
                            'product_variants.*.unit_price'    => 'required',
                            'product_variants.*.total_amount'    => 'required',
                            'product_variants.*.tax_amount'    => 'required',
                            'product_variants.*.discount_amount'    => 'required',
                            //  'product_variants.*.discount_percentage'    =>'required',
                        ],
                        [
                            'customer_id.required'  => 'Customer required',
                            'total_amount.required' => 'Total order amount required',
                            'payment_type_id.required'  => 'Payment type required',
                            'status_id.required'    => 'Status required',
                            'product_variants.*.cart_id.required'    => 'Cart ID required',
                            'product_variants.*.product_id.required'    => 'Product required',
                            'product_variants.*.product_varient_id.required'    => 'Product variant required',
                            'product_variants.*.quantity.required'    => 'Product quantity required',
                            'product_variants.*.unit_price.required'    => 'Product quantity required',
                            'product_variants.*.total_amount.required'    => 'Total amount required',
                            'product_variants.*.tax_amount.required'    => 'Tax amount required',
                            'product_variants.*.discount_amount.required'    => 'Discount amount required',
                            // 'product_variants.*.discount_percentage.required'    =>'Discount percentage required',
                        ]
                    );
    
                    if (!$validator->fails()) {
                        $noStockProducts = array();
                        $cust=Trn_store_customer::where('customer_id',$request->customer_id)->first();
                        if($cust)
                        {
                            if($cust->customer_profile_status==0)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Profile is not active";
                                return response($data);
                            }
    
                        }
                        $slot=[];
                        if($request->time_slot)
                        {
                            $slot=Trn_StoreDeliveryTimeSlot::find($request->time_slot);

                        }
                        $pua=0;
                        $remCount=0;
                        $i_check=0;
                        $service_purchase_delivery_status=0;
                        foreach ($request->product_variants as $value) {
                            $varProdu = Mst_store_product_varient::find($value['product_varient_id']);
                            $proData = Mst_store_product::find($varProdu->product_id);
                            $start = $proData->timeslot_start_time; //init the start time
                            $end = $proData->timeslot_end_time; //init the end time
                            //return $start;
                            if($request->time_slot==0)
                            {
                                $currTime = date("G:i");
                                
                                //return $start;
                                if($proData->is_timeslot_based_product==1)
                                {
                               
                                if ($currTime<$start || $currTime>$end)
                                {
                                    $pua=$pua+1;
                            
                                }
                                   
                            
                                }


                            }
                           
                            if ($proData->is_timeslot_based_product==1)
                            {
                                if($slot)
                                {
                                if($slot->time_start<$start || $slot->time_end>$end)
                                {
                                    $pua=$pua+1;
                                }
                             }
                            
                               
                            }
                            //return $pua;
                            if($varProdu )
                            {
                                if($proData->product_type==2)
                                {
                                    if($proData->service_purchase_delivery_status==0)
                                    {
                                        $data['status'] = 0;
                                        $data['message'] = "Delivery is not available now for this purchase product..Try again!!!!!!!!!";
                                        DB::rollback();
                                        return response($data);

                                    }
                                    else
                                    {
                                        $service_purchase_delivery_status=1;
                                    }
                                    $varProdu->stock_count=$value['quantity'];

                                }
                                $stockDiffernece=$varProdu ->stock_count-$value['quantity'];
                                
                                if ($request->payment_type_id != 2) {
                                if($stockDiffernece<0)
                                {
                                    $data['status'] = 0;
                                    $data['message'] = "Some products quantity is more than available stock..Try again!!!!!!!!!";
                                    DB::rollback();
                                    return response($data);
    
                                }
                            }
                             if($varProdu->is_removed==1)
                             {
                                $remCount=$remCount+1;

                            }
                            } 
                            
                        }
                        //return $pua;
                        if($remCount>0)
                        {
                            $data['status'] = 0;
                            $data['message'] = "FEW PRODUCTS IN CART ARE REMOVED FROM STORE";
                            DB::rollback();
                            return response($data);

                        }
                        if($request->accept==0)
                        {
                        if($pua>0)
                        {
                            $data['status'] = 3;
                            $data['message'] = "FEW PRODUCTS IN CART ARE UNAVAILABLE ON THE SELECTED TIMESLOT. PLEASE CONFIRM BY ACCEPTING OR DECLINING";
                            DB::rollback();
                            return response($data);

                        }
                    }
    
    
                        if (count($noStockProducts) > 0) {
                            $data['noStockProducts'] = $noStockProducts;
                            return response($data);
                        }
                        //  $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();
    
                        //  $orderNumber = @$storeOrderCount + 1;
    
                        $store_data = Mst_store::find($request->store_id);
    
                        if (isset($store_data->order_number_prefix)) {
                            $orderNumberPrefix = $store_data->order_number_prefix;
                        } else {
                            $orderNumberPrefix = 'ORDRYSTR';
                        }
                        // $last_order_number=Helper::checkOrderNumber($request->store_id);
                        // $orderNumber = $last_order_number + 1;
                        $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();

                    $orderNumber = @$storeOrderCount + 1;
                        
    
                        //check for any locked orders
                        if(isset($request->lock_order_id) && $request->lock_order_id != 0) //if the order is locked release lock
                        {
                            // $order_no_exists=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                            // if($order_no_exists)
                            // {
                            //   $orderNumber=$orderNumber+1;
                            // }
                            Trn_store_order::withTrashed()->where('order_id','=',$request->lock_order_id)->update([
                                'deleted_at' => NULL,
                                'is_locked' => 0
                            ]);
                            
                            $fetchOrder = Trn_store_order::where('order_id','=',$request->lock_order_id)->first();
                           
                            $order_id = $fetchOrder->order_id;

                            $update_order =  Trn_store_order::find($order_id);
                            $update_order->referenceId = $request->referenceId;
                            $update_order->order_number=$orderNumberPrefix .substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4). @$orderNumber;
                            $update_order->txTime = $request->txTime;
                            $update_order->trn_id = $request->orderId;
                            $update_order->orderAmount = $request->orderAmount;
                            $update_order->txMsg = $request->txMsg;
                            $update_order->txStatus = $request->txStatus;
                            $update_order->created_at=Carbon::now();
                            $update_order->updated_at=Carbon::now();
                            $update_order->save();

                            $invoice_info['order_id'] = $order_id;
                            $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
                            $invoice_info['invoice_id'] = "INV0" . $order_id;
                            $invoice_info['created_at'] = Carbon::now();
                            $invoice_info['updated_at'] = Carbon::now();
        
                            Trn_order_invoice::insert($invoice_info);
                            DB::commit();
                            
            
            
                        }else{ //place a new order
                        // $order_no_exists=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                        // if($order_no_exists)
                        // {
                        //   $orderNumber=$orderNumber+1;
                        // }
                            
                            $store_order = new Trn_store_order;
    
                        if (isset($request->service_order))
                            $store_order->service_order =  $request->service_order; // service order - booking order
    
    
                        $store_order->order_number = $orderNumberPrefix .substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4). @$orderNumber;
                        // $store_order->order_number = 'ORDRYSTR'.@$orderNumber;
                        $store_order->customer_id = $request->customer_id;
                        $store_order->store_id =  $request->store_id;
    
    
                        $store_order->subadmin_id =  $store_data->subadmin_id;
                        $store_order->product_total_amount =  $request->order_total_amount;
                        $store_order->payment_status = 1;
                        $store_order->status_id = $request->status_id;
    
                        // online
                        $store_order->payment_type_id = $request->payment_type_id;
                        $store_order->delivery_charge =  $request->delivery_charge;
                        $store_order->packing_charge =  $request->packing_charge;
                        $store_order->order_service_purchase_delivery_availability=$request->service_purchase_delivery_status??223;
                        $store_order->time_slot =  $request->time_slot;

                        $store_order->delivery_option=$service_purchase_delivery_status??0;
                        $store_order->future_delivery_date=$request->future_delivery_date??NULL;
                        $store_order->is_collect_from_store=$request->is_collect_from_store;
                        $store_order->immediate_store_text=$orderStoreData->immediate_delivery_text;
                        $store_order->delivery_address =  $request->delivery_address;
    
                        $store_order->coupon_id =  $request->coupon_id;
                        $store_order->coupon_code =  $request->coupon_code;
    
                        if ($request->status_id != 5) {
                            $store_order->reward_points_used =  $request->reward_points_used;
                            $store_order->reward_points_used_store =  $request->reward_points_used_store;
                            $store_order->amount_before_applying_rp =  $request->amount_before_applying_rp;
                            $store_order->amount_reduced_by_rp =  $request->amount_reduced_by_rp;
                            $store_order->amount_reduced_by_rp_store =  $request->amount_reduced_by_rp_store;
                            
                        } else
                         {
                            $store_order->reward_points_used =  0;
                            $store_order->reward_points_used_store = 0;
                            $store_order->amount_before_applying_rp =  0;
                            $store_order->amount_reduced_by_rp =  0;
                            $store_order->amount_reduced_by_rp_store =  0;
                        }
    
    
                        $store_order->order_type = 'APP';
                        $store_order->trn_id = $request->orderId;
    
                        if ($request->payment_type_id == 2) {
    
                            if (Helper::isBankDataFilled($request->store_id) == 1) {
                                $store_order->is_split_data_saved = 0;
                            } else {
                                $store_order->is_split_data_saved = 1;
                            }
                        }
    
                        $store_order->referenceId = $request->referenceId;
                        $store_order->txTime = $request->txTime;
                        $store_order->orderAmount = $request->orderAmount;
                        $store_order->txMsg = $request->txMsg;
                        $store_order->txStatus = $request->txStatus;
                        $store_order->created_at=Carbon::now();
                        $store_order->updated_at=Carbon::now();
                        //(int)substr(Helper::latestOrder(7), -1)
    
                        if (isset($request->amount_reduced_by_coupon))
                            $store_order->amount_reduced_by_coupon =  $request->amount_reduced_by_coupon;
    
                        $store_order->save();

                        $order_id = DB::getPdo()->lastInsertId();
                        // $exist_last=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                        // $order_id = $exist_last->order_id;
                        
                        //delete cart items
                        Trn_Cart::where('customer_id', $request->customer_id)
                                ->update(['remove_status' =>  1]); //deleted
    
    
                        $invoice_info['order_id'] = $order_id;
                        $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
                        $invoice_info['invoice_id'] = "INV0" . $order_id;
                        $invoice_info['created_at'] = Carbon::now();
                        $invoice_info['updated_at'] = Carbon::now();
    
                        Trn_order_invoice::insert($invoice_info);
                        //Product availability time slot check done

                        foreach ($request->product_variants as $value) {
                            $productVarOlddata = Mst_store_product_varient::find($value['product_varient_id']);
                            if($proData->product_type==2)
                            {
                                $productVarOlddata->stock_count=$value['quantity'];
                            }
                            $stockDiffernece=$productVarOlddata->stock_count-$value['quantity'];
                            
                            if ($request->payment_type_id == 1) {
                                if($stockDiffernece<0)
                                {
                                    $data['status'] = 0;
                                    $data['message'] = "Some products quantity is more than available stock..Try again";
                                    DB::rollback();
                                    return response($data);
    
                                }
                                if($stockDiffernece==0)
                                {
                                    DB::table('mst__stock_details')->where('product_varient_id', $value['product_varient_id'])->update(['created_at' => Carbon::now()]);
                                    $s = DB::table('mst_store_product_varients')->where('product_varient_id', $value['product_varient_id'])->pluck("stock_count");
                                    Db::table('empty_stock_log')->where('product_varient_id',$value['product_varient_id'])->delete();
                                    DB::table('empty_stock_log')->insert(['product_varient_id'=>$value['product_varient_id'],'created_time' => Carbon::now()]);
                                }
                            }

                            
    
                            if ($proData->service_type != 2) {
                                Mst_store_product_varient::where('product_varient_id', '=', $value['product_varient_id'])->decrement('stock_count', $value['quantity']);
                                $prv=Mst_store_product_varient::where('product_varient_id', '=', $value['product_varient_id'])->first();
                                if($prv->stock_count<0)
                                {
                                    Mst_store_product_varient::where('product_varient_id', '=', $value['product_varient_id'])->increment('stock_count', $value['quantity']);
                                    $data['status'] = 0;
                                    $data['message'] = "Some products quantity is more than available stock..Try again Later!";
                                    DB::rollback();
                                    return response($data);

                                }

                            }
    

                            if (!isset($value['discount_amount'])) {
                                $value['discount_amount'] = 0;
                            }
    
                            if ($proData->service_type != 2) {
                                $negStock = -1 * abs($value['quantity']);
    
                                $sd = new Mst_StockDetail;
                                $sd->store_id = $request->store_id;
                                $sd->product_id = $value['product_id'];
                                $sd->stock = $negStock;
                                $sd->product_varient_id = $value['product_varient_id'];
                                $sd->prev_stock = $productVarOlddata->stock_count;
                                $sd->save();
                            }
    
                            $proDataZ = Mst_store_product::find($productVarOlddata->product_id);
    
                            $taxData = Mst_Tax::find($proDataZ->tax_id);
    
                            $total_amount = $value['quantity'] * $value['unit_price'];
    
                            // $iTax = @$productVarOlddata->product_varient_offer_price * 100 / (100 + @$taxData->tax_value);
                            $iTax = @$productVarOlddata->product_varient_offer_price * @$taxData->tax_value / (100 + @$taxData->tax_value);
                            $iDis = @$productVarOlddata->product_varient_price - @$productVarOlddata->product_varient_offer_price;
    
                            $data2 = [
                                'order_id' => $order_id,
                                'cart_id' => $value['cart_id'],
                                'product_id' => $value['product_id'],
                                'product_varient_id' => $value['product_varient_id'],
                                'customer_id' => $request['customer_id'],
                                'store_id' => $request['store_id'],
                                'quantity' => $value['quantity'],
                                'unit_price' =>  $value['unit_price'],
                                'product_name'=>$productVarOlddata->variant_name,
                                'product_image'=>$productVarOlddata->product_varient_base_image,
                                'mrp'=>$productVarOlddata->product_varient_price,
                                'tax_value'=>@$taxData->tax_value,
                                'tax_id'=>$proDataZ->tax_id,
                                'tax_amount' => $iTax,
                                'total_amount' => $total_amount,
                                'discount_amount' => $iDis,
                                'created_at'         => Carbon::now(),
                                'updated_at'         => Carbon::now(),
                                'is_timeslot_product'=>$proDataZ->is_timeslot_based_product,
                                'time_start'=>$proDataZ->timeslot_start_time??NULL,
                                'time_end'=>$proDataZ->timeslot_end_time??NULL,

                            ];
                            Trn_store_order_item::insert($data2);
                        }

                        }
    
                        
    
                        
    
    
    
    
                        if ($request->payment_type_id == 2) { //online
    
                            if (Helper::isBankDataFilled($request->store_id) == 0) {
                                $opt = new Trn_OrderPaymentTransaction;
                                $opt->order_id = $order_id;
                                $opt->paymentMode = $request->paymentMode;
                                $opt->PGOrderId = $request->orderId;
                                $opt->txTime = $request->txTime;
                                $opt->referenceId = $request->referenceId;
                                $opt->txMsg = $request->txMsg;
                                $opt->orderAmount = $request->orderAmount;
                                $opt->txStatus = $request->txStatus;
                                $opt->isFullPaymentToAdmin = 1;
    
                                if ($opt->save()) {
                                    $opt_id = DB::getPdo()->lastInsertId();
    
                                    $adminCommission = $orderStoreData->store_commision_percentage;
                                    $orderTotalAmount = $request->orderAmount;
    
                                    $adminAmount = ($adminCommission / 100) * $orderTotalAmount;
                                    $storeBalanceAmount = $orderTotalAmount - $adminAmount;
    
                                    $osp = new Trn_OrderSplitPayments;
                                    $osp->opt_id = $opt_id;
                                    $osp->order_id = $order_id;
                                    $osp->splitAmount = $storeBalanceAmount;
                                    $osp->serviceCharge = 0;
                                    $osp->serviceTax = 0;
                                    $osp->splitServiceCharge = 0;
                                    $osp->splitServiceTax = 0;
                                    $osp->settlementAmount = $storeBalanceAmount;
                                    $osp->settlementEligibilityDate = Carbon::now()->format('Y-m-d H:i:s');
    
                                    $osp->paymentRole = 1; // 1 == store's split
                                    if ($osp->save()) {
    
                                        $osp = new Trn_OrderSplitPayments;
                                        $osp->opt_id = $opt_id;
                                        $osp->order_id = $order_id;
                                        $osp->vendorId = null;
                                        $osp->settlementId = null;
                                        $osp->splitAmount = $adminAmount;
    
                                        $osp->serviceCharge = 0;
                                        $osp->serviceTax = 0;;
                                        $osp->splitServiceCharge = 0;
                                        $osp->splitServiceTax = 0;
                                        $osp->settlementAmount = $adminAmount;
                                        $osp->settlementEligibilityDate =  Carbon::now()->format('Y-m-d H:i:s');
    
                                        $osp->paymentRole = 0;
                                        $osp->save();
                                    }
                                }
                            }
    
                            //     $opt = new Trn_OrderPaymentTransaction;
                            //     $opt->order_id = $order_id;
                            //     $opt->paymentMode = $request->paymentMode;
                            //     $opt->PGOrderId = $request->orderId;
                            //     $opt->txTime = $request->txTime;
                            //     $opt->referenceId = $request->referenceId;
                            //     $opt->txMsg = $request->txMsg;
                            //     $opt->orderAmount = $request->orderAmount;
                            //     $opt->txStatus = $request->txStatus;
                            //     if ($opt->save()) {
                            //         $opt_id = DB::getPdo()->lastInsertId();
    
                            //         $client = new \GuzzleHttp\Client();
                            //         $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/' . $request->orderId, [
                            //             'headers' => [
                            //                 'Accept' => 'application/json',
                            //                 'x-api-version' => '2021-05-21',
                            //                 'x-client-id' => '165253d13ce80549d879dba25b352561',
                            //                 'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
                            //             ],
                            //         ]);
    
                            //         $responseData = $response->getBody()->getContents();
    
                            //         $responseFinal = json_decode($responseData, true);
    
                            //         $osp = new Trn_OrderSplitPayments;
                            //         $osp->opt_id = $opt_id;
                            //         $osp->order_id = $order_id;
                            //         $osp->splitAmount = $responseFinal["settlementAmount"];
                            //         $osp->serviceCharge = $responseFinal["serviceCharge"];
                            //         $osp->serviceTax = $responseFinal["serviceTax"];
                            //         $osp->splitServiceCharge = $responseFinal["splitServiceCharge"];
                            //         $osp->splitServiceTax = $responseFinal["splitServiceTax"];
                            //         $osp->settlementAmount = $responseFinal["settlementAmount"];
                            //         $osp->settlementEligibilityDate = $responseFinal["settlementEligibilityDate"];
    
                            //         $osp->paymentRole = 1; // 1 == store's split
                            //         if ($osp->save()) {
                            //             if (count($responseFinal['vendors']) > 0) {
                            //                 foreach ($responseFinal['vendors'] as $row) {
                            //                     $osp = new Trn_OrderSplitPayments;
                            //                     $osp->opt_id = $opt_id;
                            //                     $osp->order_id = $order_id;
                            //                     $osp->vendorId = $row["id"];
                            //                     $osp->settlementId = $row["settlementId"];
                            //                     $osp->splitAmount = $row["settlementAmount"];
    
                            //                     $osp->serviceCharge = @$row["serviceCharge"];
                            //                     $osp->serviceTax = @$row["serviceTax"];
                            //                     $osp->splitServiceCharge = @$row["splitServiceCharge"];
                            //                     $osp->splitServiceTax = @$row["splitServiceTax"];
                            //                     $osp->settlementAmount = @$row["settlementAmount"];
                            //                     $osp->settlementEligibilityDate = @$row["settlementEligibilityDate"];
    
                            //                     $osp->paymentRole = 0;
                            //                     $osp->save();
                            //                 }
                            //             }
                            //         }
                            //     }
                        }
    
    
                       
    
    
    
                        $storeDatas = Trn_StoreAdmin::where('store_id', $request->store_id)->where('role_id', 0)->first();
                       $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $request->customer_id)->get();
                        $storeDevice = Trn_StoreDeviceToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $request->store_id)->get();
                        $orderdatas = Trn_store_order::find($order_id);
                        if($request->wallet_id)
                        {
                            $w_log=Trn_wallet_log::find($request->wallet_id);
                            $w_log->order_id=$order_id;
                            $w_log->update();
                            if (isset($request->reward_points_used_store) && ($request->reward_points_used_store != 0)) {
                                ///reward_points_used_store
                                foreach ($customerDevice as $cd) {
    
                                    $title = 'Store Points Deducted';
                                    $body = $request->reward_points_used_store . ' points deducted from your wallet';
                                    $clickAction = "MyWalletFragment";
                                    $type = "wallet";
                                    $data['responseStoreDeduction'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                }
                            }
                        }
                       
                        foreach ($storeDevice as $sd) {
                            $title = 'New order arrived';
                            $body = 'New order with order id ' . $orderdatas->order_number . ' has been saved successully..';
                            $clickAction = "OrdersFragment";
                            $type = "order";
                            $data['responseOrderApp'] =  $this->storeNotification($sd->store_device_token, $title, $body,$clickAction,$type);
                        }
    
    
                        $storeWeb = Trn_StoreWebToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $request->store_id)->get();
                        foreach ($storeWeb as $sw) {
                            $title = 'New order arrived';
                            $body = 'New order with order id ' . $orderdatas->order_number . ' has been saved successully..';
                            $clickAction = "OrderListFragment";
                            $type = "order";
                            $data['responseOrderWeb'] =  Helper::storeNotifyWeb($sw->store_web_token, $title, $body,$clickAction,$type);
                        }
    
    
                        sleep(3);
    
                        foreach ($customerDevice as $cd) {
                            $title = 'Order Placed';
                            $body = 'Order placed with order id ' . $orderdatas->order_number;
                            $clickAction = "OrderListFragment";
                            $type = "order";
                            $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                        }
    
    
                        if ($request->status_id != 5) {
                            if (isset($request->reward_points_used) && ($request->reward_points_used != 0)) {
    
                                foreach ($customerDevice as $cd) {
    
                                    $title = 'Points Deducted';
                                    $body = $request->reward_points_used . ' points deducted from your wallet';
                                    $clickAction = "MyWalletFragment";
                                    $type = "wallet";
                                    $data['responseAppDeduction'] =  $this->customerNotificationTest($cd->customer_device_token, $title, $body,$clickAction,$type);
                                }
                            }
                        }
                        $data['status'] = 1;
                        $data['order_id'] = $order_id;
                        $data['message'] = "Order saved.";
                        DB::commit();
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                        $data['errors'] = $validator->errors();
                        DB::rollback();
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Store not found ";
                     DB::rollback();
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
    public function saveOrder2(Request $request)
    {
        //dd($request->all());

        try {
            if(isset($request->store_id))
            {
                DB::beginTransaction();

            $isActiveSlot=Helper::findHoliday($request->store_id);
                if($isActiveSlot==false)
                {
                    $data['status'] = 0;
                    $data['message'] = "You cannot place an order now.store closed";
                    return response($data);

                }
                $getParentExpiry = Trn_StoreAdmin::where('store_id','=',$request->store_id)->where('role_id','=',0)->first();
                if($getParentExpiry)
                {
                    $today = Carbon::now()->toDateString();
                    $parentExpiryDate = $getParentExpiry->expiry_date;
                    if($today>$parentExpiryDate)
                    {
                            
                        $data['status'] = 0;
                        $data['message'] = 'Store was not avaliable from '.date('d-M-Y',strtotime($parentExpiryDate)).' You cannot place an order';
                        return response($data);          
                    }
                    
    
                }
            
           }

            if ($request->payment_type_id == 2) { //online
                //  $client = new \GuzzleHttp\Client();

                // $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/16817139', [
                //     'headers' => [
                //       'Accept' => 'application/json',
                //       'x-api-version' => '2021-05-21',
                //       'x-client-id' => '165253d13ce80549d879dba25b352561',
                //       'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
                //     ],
                //   ]);

                // $order_ID = intval($request->orderId);

                // $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/' . $order_ID, [
                //     'headers' => [
                //         'Accept' => 'application/json',
                //         'x-api-version' => '2021-05-21',
                //         'x-client-id' => '165253d13ce80549d879dba25b352561',
                //         'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
                //     ],
                // ]);

                // 'x-client-id' => '1159124beeb38480c16b093237219511',
                // 'x-client-secret' => 'f4201506d616394eebf87fa82e0b12385cd6c730'

                //   $responseData = $response->getBody()->getContents();
            }


           
                if (isset($request->store_id) && $orderStoreData = Mst_store::find($request->store_id)) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'customer_id'   => 'required',
                            'order_total_amount'  => 'required',
                            'payment_type_id'   => 'required',
                            'status_id' => 'required',
                            'product_variants.*.cart_id'    => 'required',
                            'product_variants.*.product_id'    => 'required',
                            'product_variants.*.product_varient_id'    => 'required',
                            'product_variants.*.quantity'    => 'required',
                            'product_variants.*.unit_price'    => 'required',
                            'product_variants.*.total_amount'    => 'required',
                            'product_variants.*.tax_amount'    => 'required',
                            'product_variants.*.discount_amount'    => 'required',
                            //  'product_variants.*.discount_percentage'    =>'required',
                        ],
                        [
                            'customer_id.required'  => 'Customer required',
                            'total_amount.required' => 'Total order amount required',
                            'payment_type_id.required'  => 'Payment type required',
                            'status_id.required'    => 'Status required',
                            'product_variants.*.cart_id.required'    => 'Cart ID required',
                            'product_variants.*.product_id.required'    => 'Product required',
                            'product_variants.*.product_varient_id.required'    => 'Product variant required',
                            'product_variants.*.quantity.required'    => 'Product quantity required',
                            'product_variants.*.unit_price.required'    => 'Product quantity required',
                            'product_variants.*.total_amount.required'    => 'Total amount required',
                            'product_variants.*.tax_amount.required'    => 'Tax amount required',
                            'product_variants.*.discount_amount.required'    => 'Discount amount required',
                            // 'product_variants.*.discount_percentage.required'    =>'Discount percentage required',
                        ]
                    );
    
                    if (!$validator->fails()) {
                        $noStockProducts = array();
                        $cust=Trn_store_customer::where('customer_id',$request->customer_id)->first();
                        if($cust)
                        {
                            if($cust->customer_profile_status==0)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Profile is not active";
                                return response($data);
                            }
    
                        }
    
    
                        foreach ($request->product_variants as $value) {
                            $varProdu = Mst_store_product_varient::find($value['product_varient_id']);
                            $proData = Mst_store_product::find($varProdu->product_id);
                            if($varProdu )
                            {
                                $stockDiffernece=$varProdu ->stock_count-$value['quantity'];
                                if ($request->payment_type_id != 2) {
                                if($stockDiffernece<0)
                                {
                                    $data['status'] = 0;
                                    $data['message'] = "Some products quantity is more than available stock..Try again";
                                    DB::rollback();
                                    return response($data);
    
                                }
                            }
                            }
                            
                        }
    
    
                        if (count($noStockProducts) > 0) {
                            $data['noStockProducts'] = $noStockProducts;
                            return response($data);
                        }
                        //  $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();
    
                        //  $orderNumber = @$storeOrderCount + 1;
    
                        $store_data = Mst_store::find($request->store_id);
    
                        if (isset($store_data->order_number_prefix)) {
                            $orderNumberPrefix = $store_data->order_number_prefix;
                        } else {
                            $orderNumberPrefix = 'ORDRYSTR';
                        }
                        // $last_order_number=Helper::checkOrderNumber($request->store_id);
                        // $orderNumber = $last_order_number + 1;
                        $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();

                    $orderNumber = @$storeOrderCount + 1;
                        
    
                        //check for any locked orders
                        if(isset($request->lock_order_id) && $request->lock_order_id != 0) //if the order is locked release lock
                        {
                            // $order_no_exists=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                            // if($order_no_exists)
                            // {
                            //   $orderNumber=$orderNumber+1;
                            // }
                            Trn_store_order::withTrashed()->where('order_id','=',$request->lock_order_id)->update([
                                'deleted_at' => NULL,
                                'is_locked' => 0
                            ]);
                            
                            $fetchOrder = Trn_store_order::where('order_id','=',$request->lock_order_id)->first();
                           
                            $order_id = $fetchOrder->order_id;

                            $update_order =  Trn_store_order::find($order_id);
                            $update_order->referenceId = $request->referenceId;
                            $update_order->order_number=$orderNumberPrefix .substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4). @$orderNumber;
                            $update_order->txTime = $request->txTime;
                            $update_order->trn_id = $request->orderId;
                            $update_order->orderAmount = $request->orderAmount;
                            $update_order->txMsg = $request->txMsg;
                            $update_order->txStatus = $request->txStatus;
                            $update_order->created_at=Carbon::now();
                            $update_order->updated_at=Carbon::now();
                            $update_order->save();

                            $invoice_info['order_id'] = $order_id;
                            $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
                            $invoice_info['invoice_id'] = "INV0" . $order_id;
                            $invoice_info['created_at'] = Carbon::now();
                            $invoice_info['updated_at'] = Carbon::now();
        
                            Trn_order_invoice::insert($invoice_info);
                            DB::commit();
                            
            
            
                        }
                        else
                        { //place a new order
                        // $order_no_exists=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                        // if($order_no_exists)
                        // {
                        //   $orderNumber=$orderNumber+1;
                        // }
                            
                            $store_order = new Trn_store_order;
    
                        if (isset($request->service_order))
                            $store_order->service_order =  $request->service_order; // service order - booking order
    
    
                        $store_order->order_number = $orderNumberPrefix .substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4). @$orderNumber;
                        // $store_order->order_number = 'ORDRYSTR'.@$orderNumber;
                        $store_order->customer_id = $request->customer_id;
                        $store_order->store_id =  $request->store_id;
    
    
                        $store_order->subadmin_id =  $store_data->subadmin_id;
                        $store_order->product_total_amount =  $request->order_total_amount;
                        $store_order->payment_status = 1;
                        $store_order->status_id = $request->status_id;
    
                        // online
                        $store_order->payment_type_id = $request->payment_type_id;
                        $store_order->delivery_charge =  $request->delivery_charge;
                        $store_order->packing_charge =  $request->packing_charge;
    
                        $store_order->time_slot =  $request->time_slot;
                        $store_order->delivery_option =  $request->delivery_option;
    
    
    
                        $store_order->delivery_address =  $request->delivery_address;
    
                        $store_order->coupon_id =  $request->coupon_id;
                        $store_order->coupon_code =  $request->coupon_code;
    
                        if ($request->status_id != 5) {
                            $store_order->reward_points_used =  $request->reward_points_used;
                            $store_order->reward_points_used_store =  $request->reward_points_used_store;
                            $store_order->amount_before_applying_rp =  $request->amount_before_applying_rp;
                            $store_order->amount_reduced_by_rp =  $request->amount_reduced_by_rp;
                            $store_order->amount_reduced_by_rp_store =  $request->amount_reduced_by_rp_store;
                            
                        } else {
                            $store_order->reward_points_used =  0;
                            $store_order->reward_points_used_store = 0;
                            $store_order->amount_before_applying_rp =  0;
                            $store_order->amount_reduced_by_rp =  0;
                            $store_order->amount_reduced_by_rp_store =  0;
                        }
    
    
                        $store_order->order_type = 'APP';
                        $store_order->trn_id = $request->orderId;
    
                        if ($request->payment_type_id == 2) {
    
                            if (Helper::isBankDataFilled($request->store_id) == 1) {
                                $store_order->is_split_data_saved = 0;
                            } else {
                                $store_order->is_split_data_saved = 1;
                            }
                        }
    
                        $store_order->referenceId = $request->referenceId;
                        $store_order->txTime = $request->txTime;
                        $store_order->orderAmount = $request->orderAmount;
                        $store_order->txMsg = $request->txMsg;
                        $store_order->txStatus = $request->txStatus;
                        $store_order->created_at=Carbon::now();
                        $store_order->updated_at=Carbon::now();
                        //(int)substr(Helper::latestOrder(7), -1)
    
                        if (isset($request->amount_reduced_by_coupon))
                            $store_order->amount_reduced_by_coupon =  $request->amount_reduced_by_coupon;
    
                        $store_order->save();

                        $order_id = DB::getPdo()->lastInsertId();
                        // $exist_last=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                        // $order_id = $exist_last->order_id;
                        
                        //delete cart items
                        Trn_Cart::where('customer_id', $request->customer_id)
                                ->update(['remove_status' =>  1]); //deleted
    
    
                        $invoice_info['order_id'] = $order_id;
                        $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
                        $invoice_info['invoice_id'] = "INV0" . $order_id;
                        $invoice_info['created_at'] = Carbon::now();
                        $invoice_info['updated_at'] = Carbon::now();
    
                        Trn_order_invoice::insert($invoice_info);

                        foreach ($request->product_variants as $value) {
                            $productVarOlddata = Mst_store_product_varient::find($value['product_varient_id']);
                            $stockDiffernece=$productVarOlddata->stock_count-$value['quantity'];
                            if ($request->payment_type_id == 1) {
                                if($stockDiffernece<0)
                                {
                                    $data['status'] = 0;
                                    $data['message'] = "Some products quantity is more than available stock..Try again";
                                    DB::rollback();
                                    return response($data);
    
                                }
                            }
                            
    
                            if ($proData->service_type != 2) {
                                Mst_store_product_varient::where('product_varient_id', '=', $value['product_varient_id'])->decrement('stock_count', $value['quantity']);
                            }
    

                            if (!isset($value['discount_amount'])) {
                                $value['discount_amount'] = 0;
                            }
    
                            if ($proData->service_type != 2) {
                                $negStock = -1 * abs($value['quantity']);
    
                                $sd = new Mst_StockDetail;
                                $sd->store_id = $request->store_id;
                                $sd->product_id = $value['product_id'];
                                $sd->stock = $negStock;
                                $sd->product_varient_id = $value['product_varient_id'];
                                $sd->prev_stock = $productVarOlddata->stock_count;
                                $sd->save();
                            }
    
                            $proDataZ = Mst_store_product::find($productVarOlddata->product_id);
    
                            $taxData = Mst_Tax::find($proDataZ->tax_id);
    
                            $total_amount = $value['quantity'] * $value['unit_price'];
    
                            // $iTax = @$productVarOlddata->product_varient_offer_price * 100 / (100 + @$taxData->tax_value);
                            $iTax = @$productVarOlddata->product_varient_offer_price * @$taxData->tax_value / (100 + @$taxData->tax_value);
                            $iDis = @$productVarOlddata->product_varient_price - @$productVarOlddata->product_varient_offer_price;
    
                            $data2 = [
                                'order_id' => $order_id,
                                'cart_id' => $value['cart_id'],
                                'product_id' => $value['product_id'],
                                'product_varient_id' => $value['product_varient_id'],
                                'customer_id' => $request['customer_id'],
                                'store_id' => $request['store_id'],
                                'quantity' => $value['quantity'],
                                'unit_price' =>  $value['unit_price'],
                                'mrp'=>$productVarOlddata->product_varient_price,
                                'tax_value'=>@$taxData->tax_value,
                                'tax_id'=>$proDataZ->tax_id,
                                'tax_amount' => $iTax,
                                'total_amount' => $total_amount,
                                'discount_amount' => $iDis,
                                'created_at'         => Carbon::now(),
                                'updated_at'         => Carbon::now(),
                            ];
                            Trn_store_order_item::insert($data2);
                        }

                        }
    
                        
    
                        
    
    
    
    
                        if ($request->payment_type_id == 2) { //online
    
                            if (Helper::isBankDataFilled($request->store_id) == 0) {
                                $opt = new Trn_OrderPaymentTransaction;
                                $opt->order_id = $order_id;
                                $opt->paymentMode = $request->paymentMode;
                                $opt->PGOrderId = $request->orderId;
                                $opt->txTime = $request->txTime;
                                $opt->referenceId = $request->referenceId;
                                $opt->txMsg = $request->txMsg;
                                $opt->orderAmount = $request->orderAmount;
                                $opt->txStatus = $request->txStatus;
                                $opt->isFullPaymentToAdmin = 1;
    
                                if ($opt->save()) {
                                    $opt_id = DB::getPdo()->lastInsertId();
    
                                    $adminCommission = $orderStoreData->store_commision_percentage;
                                    $orderTotalAmount = $request->orderAmount;
    
                                    $adminAmount = ($adminCommission / 100) * $orderTotalAmount;
                                    $storeBalanceAmount = $orderTotalAmount - $adminAmount;
    
                                    $osp = new Trn_OrderSplitPayments;
                                    $osp->opt_id = $opt_id;
                                    $osp->order_id = $order_id;
                                    $osp->splitAmount = $storeBalanceAmount;
                                    $osp->serviceCharge = 0;
                                    $osp->serviceTax = 0;
                                    $osp->splitServiceCharge = 0;
                                    $osp->splitServiceTax = 0;
                                    $osp->settlementAmount = $storeBalanceAmount;
                                    $osp->settlementEligibilityDate = Carbon::now()->format('Y-m-d H:i:s');
    
                                    $osp->paymentRole = 1; // 1 == store's split
                                    if ($osp->save()) {
    
                                        $osp = new Trn_OrderSplitPayments;
                                        $osp->opt_id = $opt_id;
                                        $osp->order_id = $order_id;
                                        $osp->vendorId = null;
                                        $osp->settlementId = null;
                                        $osp->splitAmount = $adminAmount;
    
                                        $osp->serviceCharge = 0;
                                        $osp->serviceTax = 0;;
                                        $osp->splitServiceCharge = 0;
                                        $osp->splitServiceTax = 0;
                                        $osp->settlementAmount = $adminAmount;
                                        $osp->settlementEligibilityDate =  Carbon::now()->format('Y-m-d H:i:s');
    
                                        $osp->paymentRole = 0;
                                        $osp->save();
                                    }
                                }
                            }
    
                            //     $opt = new Trn_OrderPaymentTransaction;
                            //     $opt->order_id = $order_id;
                            //     $opt->paymentMode = $request->paymentMode;
                            //     $opt->PGOrderId = $request->orderId;
                            //     $opt->txTime = $request->txTime;
                            //     $opt->referenceId = $request->referenceId;
                            //     $opt->txMsg = $request->txMsg;
                            //     $opt->orderAmount = $request->orderAmount;
                            //     $opt->txStatus = $request->txStatus;
                            //     if ($opt->save()) {
                            //         $opt_id = DB::getPdo()->lastInsertId();
    
                            //         $client = new \GuzzleHttp\Client();
                            //         $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/' . $request->orderId, [
                            //             'headers' => [
                            //                 'Accept' => 'application/json',
                            //                 'x-api-version' => '2021-05-21',
                            //                 'x-client-id' => '165253d13ce80549d879dba25b352561',
                            //                 'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
                            //             ],
                            //         ]);
    
                            //         $responseData = $response->getBody()->getContents();
    
                            //         $responseFinal = json_decode($responseData, true);
    
                            //         $osp = new Trn_OrderSplitPayments;
                            //         $osp->opt_id = $opt_id;
                            //         $osp->order_id = $order_id;
                            //         $osp->splitAmount = $responseFinal["settlementAmount"];
                            //         $osp->serviceCharge = $responseFinal["serviceCharge"];
                            //         $osp->serviceTax = $responseFinal["serviceTax"];
                            //         $osp->splitServiceCharge = $responseFinal["splitServiceCharge"];
                            //         $osp->splitServiceTax = $responseFinal["splitServiceTax"];
                            //         $osp->settlementAmount = $responseFinal["settlementAmount"];
                            //         $osp->settlementEligibilityDate = $responseFinal["settlementEligibilityDate"];
    
                            //         $osp->paymentRole = 1; // 1 == store's split
                            //         if ($osp->save()) {
                            //             if (count($responseFinal['vendors']) > 0) {
                            //                 foreach ($responseFinal['vendors'] as $row) {
                            //                     $osp = new Trn_OrderSplitPayments;
                            //                     $osp->opt_id = $opt_id;
                            //                     $osp->order_id = $order_id;
                            //                     $osp->vendorId = $row["id"];
                            //                     $osp->settlementId = $row["settlementId"];
                            //                     $osp->splitAmount = $row["settlementAmount"];
    
                            //                     $osp->serviceCharge = @$row["serviceCharge"];
                            //                     $osp->serviceTax = @$row["serviceTax"];
                            //                     $osp->splitServiceCharge = @$row["splitServiceCharge"];
                            //                     $osp->splitServiceTax = @$row["splitServiceTax"];
                            //                     $osp->settlementAmount = @$row["settlementAmount"];
                            //                     $osp->settlementEligibilityDate = @$row["settlementEligibilityDate"];
    
                            //                     $osp->paymentRole = 0;
                            //                     $osp->save();
                            //                 }
                            //             }
                            //         }
                            //     }
                        }
    
    
                       
    
    
    
                        $storeDatas = Trn_StoreAdmin::where('store_id', $request->store_id)->where('role_id', 0)->first();
                        $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $request->customer_id)->get();
                        $storeDevice = Trn_StoreDeviceToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $request->store_id)->get();
                        $orderdatas = Trn_store_order::find($order_id);
                        if($request->wallet_id)
                        {
                            $w_log=Trn_wallet_log::find($request->wallet_id);
                            $w_log->order_id=$order_id;
                            $w_log->update();
                        }
    
                        foreach ($storeDevice as $sd) {
                            $title = 'New order arrived';
                            $body = 'New order with order id ' . $orderdatas->order_number . ' has been saved successully..';
                            $clickAction = "OrdersFragment";
                            $type = "order";
                            $data['response'] =  $this->storeNotification($sd->store_device_token, $title, $body,$clickAction,$type);
                        }
    
    
                        $storeWeb = Trn_StoreWebToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $request->store_id)->get();
                        foreach ($storeWeb as $sw) {
                            $title = 'New order arrived';
                            $body = 'New order with order id ' . $orderdatas->order_number . ' has been saved successully..';
                            $clickAction = "OrderListFragment";
                            $type = "order";
                            $data['response'] =  Helper::storeNotifyWeb($sw->store_web_token, $title, $body,$clickAction,$type);
                        }
    
    
    
    
                        foreach ($customerDevice as $cd) {
                            $title = 'Order Placed';
                            $body = 'Order placed with order id ' . $orderdatas->order_number;
                            $clickAction = "OrderListFragment";
                            $type = "order";
                            $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                        }
    
    
                        if ($request->status_id != 5) {
                            if (isset($request->reward_points_used) && ($request->reward_points_used != 0)) {
    
                                foreach ($customerDevice as $cd) {
    
                                    $title = 'Points Deducted';
                                    $body = $request->reward_points_used . ' points deducted from your wallet';
                                    $clickAction = "MyWalletFragment";
                                    $type = "wallet";
                                    $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                }
                            }
                        }
                        $data['status'] = 1;
                        $data['order_id'] = $order_id;
                        $data['message'] = "Order saved.";
                        DB::commit();
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                        $data['errors'] = $validator->errors();
                        DB::rollback();
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Store not found ";
                     DB::rollback();
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


    private function customerNotification($device_id, $title, $body,$clickAction,$type)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = 'AAAA09gixf4:APA91bFiBdhtMnj2UBtqSQ9YlZ_uxvdOOOzE-otA9Ja2w0cFUpX230Xv0Yi87owPBlFDp1H02FWpv4m8azPsuMmeAmz0msoeF-1Cxx0iVpDSOjYBTCWxzUYT8tKTuUvLb08MDsRXHbgM';
        $fields = array(
            'to' => $device_id,
            'notification' => array('title' => $title, 'body' => $body, 'sound' => 'default', 'click_action' => $clickAction),
            'data' => array('title' => $title, 'body' => $body,'type' => $type),
        );
        $headers = array(


            'Content-Type:application/json',
            'Authorization:key=' . $api_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
    private function customerNotificationTest($device_id, $title, $body,$clickAction,$type)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = 'AAAA09gixf4:APA91bFiBdhtMnj2UBtqSQ9YlZ_uxvdOOOzE-otA9Ja2w0cFUpX230Xv0Yi87owPBlFDp1H02FWpv4m8azPsuMmeAmz0msoeF-1Cxx0iVpDSOjYBTCWxzUYT8tKTuUvLb08MDsRXHbgM';
        $custom_sound_url = 'https://hexprojects.in/Yellowstore/assets/order_confirmed.mp3'; // Update this with the URL of your custom sound file
        $fields = array(
            'to' => $device_id,
            'notification' => array('title' => $title, 'body' => $body, 'sound' => $custom_sound_url, 'click_action' => $clickAction),
            'data' => array('title' => $title, 'body' => $body,'type' => $type),
        );
        $headers = array(


            'Content-Type:application/json',
            'Authorization:key=' . $api_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }


    private function storeNotification($device_id, $title, $body,$clickAction,$type)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = 'AAAAnXagbe8:APA91bEqMgI9Wb_psiCzKPNCQcoFt3W7RwG08oucA_UHwMjTBIbLyalZgMnigItD-0e8SDrWPfxHrT4g5zlfXHovUITXLuB32RdWp3abYyqJh2xIy_tAsGuPJJdnV5sNGxrnrrnExYYm';
        $fields = array(
            'to' => $device_id,
            'notification' => array('title' => $title, 'body' => $body, 'sound' => 'default', 'click_action' => $clickAction),
            'data' => array('title' => $title, 'body' => $body,'type' => $type),
        );
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $api_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

   public function testNotification()
   {
    $data=[];
    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', 60)->get();
    foreach ($customerDevice as $cd) {
        $title = 'Testing';
        $body = 'testing.....';
        $clickAction = "OrderListFragment";
        $type = "order";
        $data['response'] =  $this->customerNotificationTest($cd->customer_device_token, $title, $body,$clickAction,$type);
    }
    return $data;

   }
    public function stockAvailability(Request $request)
    {
        $data = array();
        $unAvailableProduct=[];
        $noStockProducts = array();
        try {
            if(isset($request->store_id))
            {
                
            foreach ($request->product_variants as $value) {

                $varProdu = Mst_store_product_varient::lockForUpdate()->find($value['product_varient_id']);
                $proData = Mst_store_product::find($varProdu->product_id);
                if($proData->product_status==0)
                       {
                           if($varProdu->is_base_variant==1)
                           {
                               $varProdu->variant_status=0;

                           }

                       }
                if ($value['quantity'] > $varProdu->stock_count || $proData->is_removed==1||$varProdu->variant_status==0) {
                    array_push($unAvailableProduct,$varProdu->product_varient_id); 
                }
                if ($value['quantity'] > $varProdu->stock_count) {
                    if($proData->product_type==1)
                    {
                        array_push($noStockProducts,$varProdu->product_varient_id); 

                    }
                    
                }
                if ($varProdu->is_removed==1) {
                    array_push($unAvailableProduct,$varProdu->product_varient_id); 
                }
            }
            $data['unAvailableProducts']=$unAvailableProduct;
        }
        // if (isset($request->store_id) && $storestatus = Mst_store::find($request->store_id)) {
        //     //check status of the store 
        //     Mst_store::where('store_id','=',$request->store_id)->where('store_account_status','=',1)
          

        //     } else {
        //                 $data['status'] = 0;
        //                 $data['message'] = "Store Inactive ";
        //                 return response($data);
        //             }


            
            $remCount=0;
            if($request->store_id)
            {
                $store=Mst_store::find($request->store_id);
                if($store->online_status==0)
                {
                    $data['status'] = 16;
                    $data['message'] = 'Store is offline now.Not possible to place an order.Try again later!';
                    return response($data);     

                }

                $isActiveSlot=Helper::findHoliday($request->store_id);
                if($isActiveSlot==false)
                {
                    $data['status'] = 17;
                    $data['message'] = "You cannot place an order now.store closed";
                    return response($data);

                }
                $getParentExpiry = Trn_StoreAdmin::where('store_id','=',$request->store_id)->where('role_id','=',0)->first();
                if($getParentExpiry)
                {
                    $today = Carbon::now()->toDateString();
                    $parentExpiryDate = $getParentExpiry->expiry_date;
                    if($today>$parentExpiryDate)
                    {
                            
                        $data['status'] = 18;
                        $data['message'] = 'Store was not avaliable from '.date('d-M-Y',strtotime($parentExpiryDate)).' You cannot place an order';
                        return response($data);          
                    }
                    if($getParentExpiry->store_account_status==0)
                    {
                        $data['status'] = 20;
                        $data['message'] = 'Store is inactive.Not possible to place an order!';
                        return response($data);       
                                    
                    }
                    
    
                }
            }

            if(isset($request->store_id))
           
            $data['unAvailableProducts']=$unAvailableProduct;
            $data['noStockProducts'] = $noStockProducts;
            
            foreach ($request->product_variants as $value) {
                $varProdu = Mst_store_product_varient::lockForUpdate()->find($value['product_varient_id']);
                $proData = Mst_store_product::find($varProdu->product_id);

                


                    if (isset($varProdu)) {
                       // || $proData->product_status == 1
                       //check varient status
                       if($varProdu->is_removed==1)
                       {
                          $remCount=$remCount+1;
                          if($remCount>0)
                          {
                            $data['status'] = 6;
                            $data['message'] = "FEW PRODUCTS IN CART ARE REMOVED FROM STORE";
                            return response($data);
                          }
                         

                      }
                       if($proData->product_status==0)
                       {
                           if($varProdu->is_base_variant==1)
                           {
                               $varProdu->variant_status=0;

                           }

                       }
                       if($varProdu->variant_status == 0)
                       {
                       
                        $data['product_name'] = @$varProdu->variant_name;

                        //$noStockProducts[] = $varProdu->product_varient_id;
                        //$data['noStockProducts'] = $noStockProducts; // first commented.  uncommented to solve unexpected error in cart proceed due to product unavailability.
                        $data['message'] = 'Product unavailable';
                        $data['status'] = 2;
                        return response($data);
                       }
                      if($proData->display_flag == 0)
                    {
                    $data['product_name'] = @$varProdu->variant_name;

                    // $noStockProducts[] = @$varProdu->product_varient_id;

                    //$data['noStockProducts'] = $noStockProducts;
                    $data['message'] = 'Product unavailable';
                    $data['status'] = 2;
                    return response($data);
                      }
                       if ($proData->service_type != 2) {

                        if ($value['quantity'] > $varProdu->stock_count || $proData->product_status==0||$proData->is_removed==1) {

                           
                                    $data['product_name'] = @$varProdu->variant_name;

                                    //$noStockProducts[] = $varProdu->product_varient_id;
        
                                    //$data['noStockProducts'] = $noStockProducts;
                                    $data['message'] = 'Stock unavailable';
                                    $data['status'] = 2;
                                         
                                    
        
                                
        
                            
                           
                          
                        }
                    }
                       
                       

                        if (isset($value['price'])) {
                            if ($varProdu->product_varient_offer_price != $value['price']) {
                                $data['product_name'] = @$varProdu->variant_name;

                                //$noStockProducts[] = $varProdu->product_varient_id;

                                //$//data['noStockProducts'] = $noStockProducts;
                                $data['message'] = 'Stock unavailable..';
                                $data['price'] = @$value['price'];
                                $data['currentPrice'] = @$varProdu->product_varient_offer_price;
                                $data['status'] = 2;
                                return response($data);
                            }
                        }



                    } else {
                        $data['message'] = 'Product not found';
                        $data['status'] = 2;
                        return response($data);
                    }
                }
        
           
        if ($proData->service_type != 2) {
            if (count($noStockProducts) <= 0) {
                $data['message'] = 'Stock available';
                $data['status'] = 1;
             }
            }
            if ($proData->service_type == 2) {
                
                    $data['message'] = 'Service Product available';
                    $data['status'] = 1;
                 
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

    public function issueTypes(Request $request)
    {
        $data = array();
        try {

            if ($data['issueTypes']  = Sys_IssueType::all()) {

                $data['status'] = 1;
                $data['message'] = "success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "failed";
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

    public function issues(Request $request)
    {
        $data = array();
        try {

            if (isset($request->issue_type_id) && Sys_IssueType::find($request->issue_type_id)) {
                if ($data['issues']  = Mst_Issues::where('issue_type_id', $request->issue_type_id)->get()) {

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
                $data['message'] = "Issue type not found";
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

    public function uploadIssue(Request $request)
    {
        try {
            // dd($request->all());
            // if(isset($request->customer_id) && Mst_store::find($request->customer_id))
            // { 
            $validator = Validator::make(
                $request->all(),
                [
                    'order_id'   => 'required',
                    'issue_id'   => 'required',
                    'customer_id'   => 'required',
                    'discription'   => 'required',

                ],
                [
                    'order_id.required'  => 'Order required',
                    'issue_id.required'  => 'Issue required',
                    'customer_id.required'  => 'Customer required',
                    'discription.required'  => 'Discription required',
                ]
            );

            if (!$validator->fails()) {
                $order_id =  $request->order_id;

                $dispute = new Mst_dispute;
                $dispute->issue_id = $request->issue_id;
                $dispute->order_id = $request->order_id;

                if (isset($request->order_item_id))
                    $dispute->order_item_id = $request->order_item_id;
                else
                    $dispute->order_item_id = 0;

                $orderData = Trn_store_order::find($request->order_id);
                $dispute->order_number = $orderData->order_number;
                $dispute->store_id = $orderData->store_id;

                $storeData = Mst_store::find($orderData->store_id);

                if (isset($storeData->subadmin_id))
                    $dispute->subadmin_id = $storeData->subadmin_id;
                else
                    $dispute->subadmin_id = 0;

                if (isset($dispute->order_item_id))
                    $dispute->item_ids = $request->order_item_id;

                // foreach($request->order_item_id as $item)
                // {
                //     $itemData = Trn_store_order_item::find($item);
                //     $productData = Mst_store_product_varient::where('product_varient_id',$itemData->product_varient_id)->first();
                //     $dispute->product_id = $itemData->product_varient_id;
                // }

                $dispute->product_id = 0;

                // if(isset($request->order_item_id) && $request->order_item_id != 0)
                // {

                // }
                // else
                // {
                //     $dispute->product_id = 0;

                // }

                $dispute->customer_id = $request->customer_id;
                $dispute->dispute_date = Carbon::now()->toDateString();
                $dispute->discription = $request->discription;
                $dispute->dispute_status = 2;
                if ($dispute->save()) {
                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $request->customer_id)->get();
                    $orderdatas = Trn_store_order::find($order_id);
                    $storeDatas = Trn_StoreAdmin::where('store_id', $orderdatas->store_id)->where('role_id', 0)->first();
                    $storeDevice = Trn_StoreDeviceToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $orderdatas->store_id)->get();
                    $storeWeb = Trn_StoreWebToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $orderdatas->store_id)->get();

                    foreach ($storeDevice as $sd) {
                        $title = 'Dispute raised';
                        $body = 'New dispute raised with order id ' . $orderdatas->order_number;
                        $clickAction = "DisputeTabFragment";
                        $type = "dispute";
                        $data['response'] =  $this->storeNotification($sd->store_device_token, $title, $body,$clickAction,$type);
                    }

                    foreach ($storeWeb as $sw) {
                        $title = 'Dispute raised';
                        $body = 'New dispute raised with order id ' . $orderdatas->order_number;
                        $clickAction = "OrderListFragment";
                        $type = "order";
                        $data['response'] =  Helper::storeNotifyWeb($sw->store_web_token, $title, $body,$clickAction,$type);
                    }

                    foreach ($customerDevice as $cd) {
                        $title = 'Dispute raised';
                        $body = 'Your dispute raised with order id ' . $orderdatas->order_number;
                        $clickAction = "OrderListFragment";
                        $type = "order";
                        $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                    }
                }


                $data['status'] = 1;
                $data['message'] = "Issue uploaded.";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "failed";
                $data['errors'] = $validator->errors();
                return response($data);
            }

            // }
            // else
            // {
            //     $data['status'] = 0;
            //     $data['message'] = "Store not found ";
            //     return response($data);
            // }

        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
    
    
    public function orderHistory2(Request $request)
    {
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $customer_id = $request->customer_id;
                $query = Trn_store_order::select(
                    'order_id',
                    'order_number',
                    'store_id',
                    'created_at',
                    'status_id',
                    'customer_id',
                    'product_total_amount',
                    'is_split_data_saved',
                    'referenceId',
                    'txTime',
                    'txMsg',
                    'orderAmount',
                    'txStatus',
                    'isRefunded',
                    'refundStatus',
                    'refundId'
                )->where('customer_id', $request->customer_id)->orderBy('created_at', 'DESC');
    
                $perPage = 10;
                $page = $request->page ?? 1;
                $data['orderHistory'] = $query->paginate($perPage, ['*'], 'page', $page);
    
                foreach ($data['orderHistory'] as $order) {
                    $storeData = Mst_store::withTrashed()->find($order->store_id);
                    if ($storeData != NULL) {
                        $order->store_name = @$storeData->store_name;
                        $order->store_code = @$storeData->store_code;
                    } else {
                        $order->store_name = 'Store not exists(Removed)';
                        $order->store_code = 'Store not exists(Removed)';
                    }
    
                    if (isset($order->customer_id)) {
                        $customerData = Trn_store_customer::find($order->customer_id);
                        $order->customer_name = @$customerData->customer_first_name . " " . @$customerData->customer_last_name;
                        $order->customer_mobile_number = @$customerData->customer_mobile_number;
                    } else {
                        $order->customer_name = null;
                    }
    
                    if (isset($order->status_id)) {
                        $statusData = Sys_store_order_status::find(@$order->status_id);
                        $order->status_name = @$statusData->status;
                    } else {
                        $order->status_name = null;
                    }
    
                    $order->order_date = Carbon::parse($order->created_at)->format('d-m-Y');
                    $order->invoice_link = url('get/invoice/' . Crypt::encryptString($order->order_id));
                }
    
                $data['status'] = 1;
                $data['message'] = "success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found";
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
    

public function orderHistory(Request $request)
{
    try {
        if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
            $customer_id = $request->customer_id;
            $query = Trn_store_order::with('order_item')->select(
                'order_id',
                'order_number',
                'store_id',
                'created_at',
                'status_id',
                'customer_id',
                'product_total_amount',
                'is_split_data_saved',
                'referenceId',
                'txTime',
                'txMsg',
                'orderAmount',
                'txStatus',
                'isRefunded',
                'refundStatus',
                'refundId'
            )->where('customer_id', $request->customer_id)->orderBy('created_at', 'DESC');

            $perPage = 10;
            $page = $request->page ?? 1;
            $offset = ($page - 1) * $perPage;
            $inventoryDatassss = collect($query->get());
            $roWc = count($inventoryDatassss);
            $dataReViStoreSS = $inventoryDatassss->slice($offset, $perPage)->values()->all();

            $data['orderHistory'] = $dataReViStoreSS;
            if ($roWc > 9) {
                $data['pageCount'] = ceil($roWc / 10);
            } else {
                $data['pageCount'] = 1;
            }

            foreach ($data['orderHistory'] as $order) {
                $storeData = Mst_store::withTrashed()->find($order->store_id);
                if ($storeData != NULL) {
                    $order->store_name = @$storeData->store_name;
                    $order->store_code = @$storeData->store_code;
                } else {
                    $order->store_name = 'Store not exists(Removed)';
                    $order->store_code = 'Store not exists(Removed)';
                }

                if (isset($order->customer_id)) {
                    $customerData = Trn_store_customer::find($order->customer_id);
                    $order->customer_name = @$customerData->customer_first_name . " " . @$customerData->customer_last_name;
                    $order->customer_mobile_number = @$customerData->customer_mobile_number;
                } else {
                    $order->customer_name = null;
                }

                if (isset($order->status_id)) {
                    $statusData = Sys_store_order_status::find(@$order->status_id);
                    $order->status_name = @$statusData->status;
                } else {
                    $order->status_name = null;
                }
                if($order->order_item!=NULL)
                {
                    if($order->order_item->product->product_type==1)
                    {

                       $order->orderProductType="Order";
                    }
                    if($order->order_item->product->product_type==2)
                    {
                        $order->orderProductType="Purchase";
                       
                    }

                }
                else
                {
                    $order->orderProductType="Booking Only";

                }

                $order->order_date = Carbon::parse($order->created_at)->format('d-m-Y');
                $order->invoice_link = url('get/invoice/' . Crypt::encryptString($order->order_id));
            }

            $data['status'] = 1;
            $data['currentPage'] = $page;
            $data['message'] = "success";
            return response($data);
        } else {
            $data['status'] = 0;
            $data['message'] = "Customer not found";
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

    

    public function orderHistory8(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $customer_id = $request->customer_id;
                if ($data['orderHistory'] = Trn_store_order::select(
                    'order_id',
                    'order_number',
                    'store_id',
                    'created_at',
                    'status_id',
                    'customer_id',
                    'product_total_amount',
                    'is_split_data_saved',
                    'referenceId',
                    'txTime',
                    'txMsg',
                    'orderAmount',
                    'txStatus',
                    'isRefunded',
                    'refundStatus',
                    'refundId'
                )->where('customer_id', $request->customer_id)->orderBy('created_at', 'DESC')->get()) {
                    foreach ($data['orderHistory'] as $order) {
                        $storeData = Mst_store::withTrashed()->find($order->store_id);
                        if($storeData!=NULL)
                        {
                            $order->store_name = @$storeData->store_name;

                        }
                        else
                        {
                            $order->store_name = 'Store not exists(Removed)';

                        }
                       
                        if (isset($order->customer_id)) {
                            $customerData = Trn_store_customer::find($order->customer_id);
                            $order->customer_name = @$customerData->customer_first_name . " " . @$customerData->customer_last_name;
                            $order->customer_mobile_number = @$customerData->customer_mobile_number;
                        } else {
                            $order->customer_name = null;
                        }

                        if (isset($order->status_id)) {
                            $statusData = Sys_store_order_status::find(@$order->status_id);
                            $order->status_name = @$statusData->status;
                        } else {
                            $order->status_name = null;
                        }
                        $order->order_date = Carbon::parse($order->created_at)->format('d-m-Y');
                        $order->invoice_link =  url('get/invoice/' . Crypt::encryptString($order->order_id));
                    }
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


    public function viewOrder(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'order_id'          => 'required',
                    ],
                    [
                        'order_id.required'        => 'Order not found',
                    ]
                );

                if (!$validator->fails() && Trn_store_order::find($request->order_id)) {
                    $order_id = $request->order_id;
                    $customer_id = $request->customer_id;
                    // dd(Trn_store_order::select('order_id','delivery_boy_id','order_note','payment_type_id','order_number','created_at','status_id','customer_id','product_total_amount')->where('order_id',$order_id)->where('store_id',$store_id)->first());

                    if ($data['orderDetails']  = Trn_store_order::select('*')->where('order_id', $order_id)->where('customer_id', $customer_id)->first()) {

                        if (!isset($data['orderDetails']->order_note))
                            $data['orderDetails']->order_note = '';


                        if (!isset($data['orderDetails']->reward_points_used))
                            $data['orderDetails']->reward_points_used = "0";
                        if (!isset($data['orderDetails']->amount_reduced_by_rp))
                            $data['orderDetails']->amount_reduced_by_rp = "0";
                        if (!isset($data['orderDetails']->reward_points_used_store))
                            $data['orderDetails']->reward_points_used_store = "0";
                        if (!isset($data['orderDetails']->amount_reduced_by_rp_store))
                            $data['orderDetails']->amount_reduced_by_rp_store = "0";



                        if ($data['orderDetails']->delivery_accept == 1) {
                            $data['orderDetails']->show_dboy_phone = 1;
                        } else {
                            $data['orderDetails']->show_dboy_phone = 0;
                        }

                        if (isset($data['orderDetails']->customer_id)) {
                            if (!isset($data['orderDetails']->amount_reduced_by_rp)) {
                                $data['orderDetails']->amount_reduced_by_rp = "0";
                            }
                            $customerData = Trn_store_customer::find($data['orderDetails']->customer_id);

                            $deliveryArrdData = Trn_customerAddress::find($data['orderDetails']->delivery_address);
                            if (!isset($deliveryArrdData)) {
                                $data['orderDetails']->customer_name = $customerData->customer_first_name . " " . $customerData->customer_last_name;
                                $data['orderDetails']->customer_mobile = @$customerData->customer_mobile_number;
                                $data['orderDetails']->customer_address = @$customerData->customer_address;
                                $data['orderDetails']->customer_pincode = @$customerData->customer_pincode;
                            } else {
                                $data['orderDetails']->customer_name = @$deliveryArrdData->name;
                                $data['orderDetails']->customer_mobile = @$deliveryArrdData->phone;

                                if (isset($deliveryArrdData->place))
                                    $data['orderDetails']->place = @$deliveryArrdData->place;
                                else
                                    $data['orderDetails']->place =    '';

                                if (isset($deliveryArrdData->districtFunction->district_name))
                                    $data['orderDetails']->district_name =   @$deliveryArrdData->districtFunction->district_name;
                                else
                                    $data['orderDetails']->district_name =    '';

                                if (isset($deliveryArrdData->stateFunction->state_name))
                                    $data['orderDetails']->state_name =     @$deliveryArrdData->stateFunction->state_name;
                                else
                                    $data['orderDetails']->state_name =    '';

                                if (isset($deliveryArrdData->stateFunction->country->country_name))
                                    $data['orderDetails']->country_name =     @$deliveryArrdData->stateFunction->country->country_name;
                                else
                                    $data['orderDetails']->country_name =    '';


                                $data['orderDetails']->customer_address = @$deliveryArrdData->address;
                                $data['orderDetails']->customer_pincode = @$deliveryArrdData->pincode;
                                $data['orderDetails']->c_latitude = @$deliveryArrdData->latitude;
                                $data['orderDetails']->c_longitude = @$deliveryArrdData->longitude;
                            }


                            $deliveryBoy = Mst_delivery_boy::find($data['orderDetails']->delivery_boy_id);
                            $data['orderDetails']->delivery_boy = @$deliveryBoy->delivery_boy_name;
                            $data['orderDetails']->delivery_boy_mobile = @$deliveryBoy->delivery_boy_mobile;
                            $dboyLoc = Trn_DeliveryBoyLocation::where('delivery_boy_id', @$deliveryBoy->delivery_boy_id)
                                ->orderBy('dbl_id', 'DESC')->first();
                            $data['orderDetails']->latitude = @$dboyLoc->latitude;
                            $data['orderDetails']->longitude = @$dboyLoc->longitude;
                        } else {
                            $data['orderDetails']->customer_name = '';
                            $data['orderDetails']->delivery_boy = '';
                            $data['orderDetails']->customer_mobile = '';
                            $data['orderDetails']->customer_address = '';
                            $data['orderDetails']->customer_pincode = '';
                        }

                        $storeData = Mst_store::withTrashed()->find($data['orderDetails']->store_id);
                        $data['orderDetails']->store_name = @$storeData->store_name;
                        $data['orderDetails']->store_code = @$storeData->store_code;
                        if (isset($storeData->gst))
                            $data['orderDetails']->gst = @$storeData->gst;
                        else
                            $data['orderDetails']->gst = "";
                        $data['orderDetails']->store_primary_address = @$storeData->store_primary_address;
                        $data['orderDetails']->store_contact_person_phone_number = @$storeData->store_contact_person_phone_number;
                        $data['orderDetails']->store_mobile = @$storeData->store_mobile;

                    if($data['orderDetails']->delivery_option==NULL)
                    {
                        if (isset($data['orderDetails']->time_slot) && ($data['orderDetails']->time_slot != 0)) {
                            $deliveryTimeSlot = Trn_StoreDeliveryTimeSlot::withTrashed()->find($data['orderDetails']->time_slot);
                            $data['orderDetails']->time_slot = @$deliveryTimeSlot->time_start . "-" . @$deliveryTimeSlot->time_end;
                            $data['orderDetails']->delivery_type = 2; //slot delivery

                        } else // timeslot null or zero
                        {
                            $data['orderDetails']->delivery_type = 1; // immediate delivery
                            $data['orderDetails']->time_slot = '';
                        }
                    }
                    else
                    {
                        if($data['orderDetails']->delivery_option==1)
                        {
                            $data['orderDetails']->delivery_type = 1; // immediate delivery
                            $data['orderDetails']->time_slot = '';

                        }
                        if($data['orderDetails']->delivery_option==2)
                        {
                            $deliveryTimeSlot = Trn_StoreDeliveryTimeSlot::withTrashed()->find($data['orderDetails']->time_slot);
                            $data['orderDetails']->time_slot = @$deliveryTimeSlot->time_start . "-" . @$deliveryTimeSlot->time_end;
                            $data['orderDetails']->delivery_type = 2; //slot delivery
                            
                        }
                        if($data['orderDetails']->delivery_option==3)
                        {
                            $deliveryTimeSlot = Trn_StoreDeliveryTimeSlot::withTrashed()->find($data['orderDetails']->time_slot);
                            $data['orderDetails']->delivery_type = 3; // Future delivery
                            $data['orderDetails']->time_slot = @$deliveryTimeSlot->time_start . "-" . @$deliveryTimeSlot->time_end;
                            
                        }
                        

                    }
                    if($data['orderDetails']->is_collect_from_store==1)
                    {
                        $data['orderDetails']->collection_type='Collect From Store';
                    }
                    else
                    {
                        $data['orderDetails']->collection_type='Pay After Delivery';

                    }

                        $data['orderDetails']->delivery_date = Carbon::parse($data['orderDetails']->delivery_date)->format('d-m-Y');
                        //$data['orderDetails']->delivery_time =  Carbon::parse($data['orderDetails']->updated_at)->format('h:i');
                        $data['orderDetails']->processed_by = null;

                        $invoice_data = \DB::table('trn_order_invoices')->where('order_id', $order_id)->first();
                        $data['orderDetails']->invoice_id = @$invoice_data->invoice_id;
                        $data['orderDetails']->invoice_date = @$invoice_data->invoice_date;

                        $orderAddress = Trn_customerAddress::find($data['orderDetails']->delivery_address);
                        if (isset($orderAddress)) {
                            $orderAddress->stateData = @$orderAddress->stateFunction['state_name'];
                            $orderAddress->districtData = @$orderAddress->districtFunction['district_name'];
                            $data['orderDetails']->orderAddress =  $orderAddress;
                        } else {
                            $data['orderDetails']->orderAddress = $orderAddress;
                        }
                        if (isset($data['orderDetails']->status_id)) {
                            $statusData = Sys_store_order_status::find($data['orderDetails']->status_id);
                            $data['orderDetails']->status_name = @$statusData->status;
                        } else {
                            $data['orderDetails']->status_name = null;
                        }
                        $data['orderDetails']->order_date = Carbon::parse($data['orderDetails']->created_at)->format('d-m-Y');

                        if ($data['orderDetails']->payment_type_id == 1)
                            $data['orderDetails']->payment_type = 'Offline';
                        else
                            $data['orderDetails']->payment_type = 'Online';


                        // dispute section
                        if ($disputeData = Mst_dispute::where('order_id', $request->order_id)->first()) {
                            $data['orderDetails']->dispute_status = 1;
                            //$data['orderDetails']->issue_id = $disputeData->issue_id; 
                            $data['orderDetails']->issue_id = @$disputeData->issues->issue_type->issue_type;
                            $data['orderDetails']->issues = @$disputeData->issues->issue;
                            if (isset($disputeData->issues->issue_type->issue_type))
                                $data['orderDetails']->issue_type = @$disputeData->issues->issue_type->issue_type;
                            else
                                $data['orderDetails']->issue_type = '';


                            if (isset($disputeData->dispute_status)) {
                                if ($disputeData->dispute_status == 1) {
                                    $data['orderDetails']->issue_status = 'Closed';
                                } elseif ($disputeData->dispute_status == 2) {
                                    $data['orderDetails']->issue_status = 'Open';
                                } elseif ($disputeData->dispute_status == 3) {
                                    $data['orderDetails']->issue_status = 'Inprogress';
                                } elseif ($disputeData->dispute_status == 4) {
                                    $data['orderDetails']->issue_status = 'Return';
                                } else {
                                    $data['orderDetails']->issue_status = '';
                                }
                            } else {
                                $data['orderDetails']->issue_status = '';
                            }

                            if (isset($disputeData->discription))
                                $data['orderDetails']->discription = $disputeData->discription;
                            else
                                $data['orderDetails']->discription = '';


                            if (isset($disputeData->store_response))
                                $data['orderDetails']->store_response = $disputeData->store_response;
                            else
                                $data['orderDetails']->store_response = '';
                        } else {
                            $data['orderDetails']->dispute_status = 0;
                            $data['orderDetails']->issue_id = '';
                            $data['orderDetails']->issues = '';
                            $data['orderDetails']->discription = '';
                            $data['orderDetails']->store_response = '';
                            $data['orderDetails']->issue_type = '';
                            $data['orderDetails']->issue_status = '';
                        }


                        $data['orderDetails']->invoice_link =  url('get/invoice/' . Crypt::encryptString($data['orderDetails']->order_id));
                        $data['orderDetails']->item_list_link = url('item/list/' . Crypt::encryptString($data['orderDetails']->order_id));

                        $data['orderDetails']->orderItems = Trn_store_order_item::where('order_id', $data['orderDetails']->order_id)
                            ->select('product_id','product_varient_id', 'order_item_id', 'quantity','product_name','product_image','discount_amount', 'discount_percentage', 'total_amount', 'tax_amount', 'unit_price','mrp','tax_value','tax_id', 'tick_status','is_timeslot_product','time_start','time_end')
                            ->get();


                        $data['orderDetails']->serviceData = new \stdClass();
                        if ($data['orderDetails']->service_booking_order == 1) {

                            $serviceData = Mst_store_product_varient::find(@$data['orderDetails']->product_varient_id);
                            
                            @$serviceData->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . @$serviceData->product_varient_base_image;
                            $baseProductDetail = Mst_store_product::find(@$serviceData->product_id);
                          
                            $serviceData->product_base_image = '/assets/uploads/products/base_product/base_image/' . @$baseProductDetail->product_base_image;
                            
                            if (@$baseProductDetail->product_name != @$serviceData->variant_name)
                                $serviceData->product_name = @$baseProductDetail->product_name . " " . @$serviceData->productDetail->variant_name;
                            else
                                $serviceData->product_name = @$baseProductDetail->product_name;
                            $data['orderDetails']->serviceData = $serviceData;
                            $data['orderDetails']->serviceData->is_timeslot_product=$baseProductDetail->is_timeslot_based_product;
                            $data['orderDetails']->serviceData->time_start=$baseProductDetail->timeslot_start_time;
                            $data['orderDetails']->serviceData->time_end=$baseProductDetail->timeslot_end_time;
                            $data['orderDetails']->serviceData->total_amount=$data['orderDetails']->product_total_amount;
                            $data['orderDetails']->serviceData->taxPercentage = $data['orderDetails']->service_tax_value;
                            $data['orderDetails']->serviceData->discountAmount = $data['orderDetails']->service_discount_amount;
                            $tTax = 1 * (@$data['orderDetails']->product_total_amount * @$data['orderDetails']->service_tax_value/ (100 + @$data['orderDetails']->service_tax_value));
                            $data['orderDetails']->serviceData->gstAmount = number_format((float)$tTax, 2, '.', '');
                            $orgCost =  1 * (100 / (100 + @$data['orderDetails']->service_tax_value));
                            $data['orderDetails']->serviceData->orgCost = number_format((float)$orgCost, 2, '.', '');
                            $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id', @$data['orderDetails']->service_tax_id)->get();
                            $stax = 0;


                            foreach ($splitdata as $sd) {
                                if (@$data['orderDetails']->service_tax_value == 0 || !isset($data['orderDetails']->service_tax_value))
                                    @$data['orderDetails']->service_tax_value = 1;

                                $stax = ($sd->split_tax_value * $tTax) / @$data['orderDetails']->service_tax_value;
                                $sd->tax_split_value = number_format((float)$stax, 2, '.', '');
                            }

                            $data['orderDetails']->serviceData->taxSplitups = $splitdata;
                            
                        }

                        $store_id = $data['orderDetails']->store_id;
                        $isServiceOrder = 0;

                        foreach ($data['orderDetails']->orderItems as $value) {

                            if ($datazz = \DB::table("mst_disputes")->where('store_id', $store_id)->where('order_id', $order_id)->first()) {

                                $colorsArray = explode(",", $datazz->item_ids);

                                $ordItemArr =  Trn_store_order_item::whereIn('order_item_id', $colorsArray)->get();
                                $colorsArray2 = array();
                                foreach ($ordItemArr as $i) {
                                    $colorsArray2[] = $i->product_varient_id;
                                }
                                if (in_array($value->product_varient_id, $colorsArray2)) {
                                    $value->dispute_status = 1;
                                } else {
                                    $value->dispute_status = 0;
                                }
                            } else {
                                $value->dispute_status = 0;
                            }



                            $value['productDetail'] = Mst_store_product_varient::find($value->product_varient_id);
                            $vaproductDetail = Mst_store_product_varient::find($value->product_varient_id);
                            if(@$value->productDetail->product_varient_base_image!=NULL)
                            {
                             
                             @$value->productDetail->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . @$value->productDetail->product_varient_base_image;
 
                            }
                            else
                            {
                             $baseProduct = Mst_store_product::find($value->product_id);
                             @$value->productDetail->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' .@$baseProduct->product_base_image;
 
                            }

                            $baseProductDetail = Mst_store_product::find($value->product_id);
                            if (($baseProductDetail->product_type == 2) && ($baseProductDetail->service_type == 2)) {
                                $isServiceOrder = 1;
                            }
                            $value->product_base_image = '/assets/uploads/products/base_product/base_image/' . @$baseProductDetail->product_base_image;

                            // if ($baseProductDetail->product_name != isset($value->productDetail->variant_name))
                            //     $value->product_name = @$baseProductDetail->product_name . " " . @$value->productDetail->variant_name;
                            // else
                            //     $value->product_name = @$baseProductDetail->product_name;
                            if($value->product_image==NULL)
                            {
                                $value->product_image = '/assets/uploads/products/base_product/base_image/' . @$baseProductDetail->product_base_image;
                              
                            }
                            else
                            {
                                $value->product_image = '/assets/uploads/products/base_product/base_image/' . @$value->product_image;

                            }
                            if($value->product_name==NULL)
                            {
                                if (@$baseProductDetail->product_name != @$value->productDetail->variant_name)
                                {
                                    $value->product_name = @$value->productDetail->variant_name;

                                }
                                else
                                {
                                    $value->product_name = @$baseProductDetail->product_name;

                                }
                                
                          
                                
                                
                            }
                            //$taxFullData = new \stdClass();
                            $taxFullData = Mst_Tax::find(@$value->tax_id);


                            $discount_amount = (@$vaproductDetail->product_varient_price - @$vaproductDetail->product_varient_offer_price) * $value->quantity;
                            //$value->discount_amount =  number_format((float)$discount_amount, 2, '.', '');
                            $value->taxPercentage = $value->tax_value;
                            $tTax = $value->quantity * (@$value->unit_price * @$value->tax_value / (100 + @$value->tax_value));
                            $value->gstAmount = number_format((float)$tTax, 2, '.', '');
                            $orgCost =  $value->quantity * (@$value->unit_price * 100 / (100 + @$value->tax_value));
                            $value->orgCost = number_format((float)$orgCost, 2, '.', '');

                            //$stax = 0;


                            $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id', @$baseProductDetail->tax_id)->get();
                            $stax = 0;


                            foreach ($splitdata as $sd) {
                                if (@$value->tax_value == 0 || !isset($value->tax_value))
                                    @$value->tax_value = 1;

                                $stax = ($sd->split_tax_value * $tTax) / @$value->tax_value;
                                $sd->tax_split_value = number_format((float)$stax, 2, '.', '');
                            }

                            $value['taxSplitups']  = $splitdata;
                        }

                        if ($isServiceOrder == 1) {
                            $data['orderDetails']->service_order = 1;
                        }


                        $data['orderPaymentTransaction'] = new \stdClass();
                        $opt = Trn_OrderPaymentTransaction::where('order_id', $request->order_id)->get();
                        $optConunt = Trn_OrderPaymentTransaction::where('order_id', $request->order_id)->count();
                        if ($optConunt > 0) {
                            foreach ($opt as $row) {
                                $ospCount = Trn_OrderSplitPayments::where('opt_id', $row->opt_id)->count();
                                if ($ospCount > 0) {
                                    $osp = Trn_OrderSplitPayments::where('opt_id', $row->opt_id)->get();
                                    $row->orderSplitPayments = $osp;
                                }
                            }
                        }
                        //Trn_OrderPaymentTransaction
                        $data['orderPaymentTransaction'] = $opt;

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
                    $data['message'] = "failed";
                    $data['message'] = "Order not found ";
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

    public function cancelOrder(Request $request)
    {
        $data = array();

        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'order_id'          => 'required',
                    ],
                    [
                        'order_id.required'        => 'Order not found',
                    ]
                );

                if (!$validator->fails() && Trn_store_order::find($request->order_id)) {
                    $order_id = $request->order_id;
                    $customer_id = $request->customer_id;
                    $orderData = Trn_store_order::find($order_id);
                    $reward=Trn_customer_reward::where('order_id')->first();
                    if($reward)
                    {
                        $reward->delete();
                    }
                    if($orderData->reward_points_used!=NULL||$orderData->reward_points_used!=0.00)
                    {
                        //$cr=new Trn_customer_reward();
                        // $cr = new Trn_customer_reward;
                        // $cr->transaction_type_id = 0;
                        // $cr->reward_points_earned = $orderData->reward_points_used;
                        // $cr->customer_id = $orderData->customer_id;
                        // $cr->order_id = $orderData->order_id;
                        // $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
                        // $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                        // $cr->reward_point_status = 1;
                        // $cr->discription = 'Cancel Recredit';
                        // $cr->save();



                    }
                    if($orderData->reward_points_used_store!=NULL||$orderData->reward_points_used_store!=0.00)
                    {
                        $scr = new Trn_customer_reward;
                        $scr->transaction_type_id = 0;
                        $scr->store_id=$orderData->store_id;
                        $scr->reward_points_earned = $orderData->reward_points_used_store;
                        $scr->customer_id = $orderData->customer_id;
                        $scr->order_id = $orderData->order_id;
                        $scr->reward_approved_date = Carbon::now()->format('Y-m-d');
                        $scr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                        $scr->reward_point_status = 1;
                        $scr->discription = 'store points';
                        $scr->save();
                        

                        $wallet_log=new Trn_wallet_log();
                        $wallet_log->store_id=$orderData->store_id;
                        $wallet_log->customer_id=$orderData->customer_id;
                        $wallet_log->order_id=$orderData->order_id;
                        $wallet_log->type='credit';
                        $wallet_log->points_debited=null;
                        $wallet_log->points_credited=$orderData->reward_points_used_store;
                        $wallet_log->save();
                        

                    }

                    $data['message'] = "Order cancelled";
                    $data['refundId'] = "";

                    if($orderData->status_id!=1)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Order is already confirmed..You cannot cancel order now";
                        return response($data);
                    }






                    if (isset($orderData->referenceId) && ($orderData->isRefunded < 2)) {


                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.cashfree.com/api/v1/order/refund',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array(
                                'appId' => '165253d13ce80549d879dba25b352561',
                                'secretKey' => 'bab0967cdc3e5559bded656346423baf0b1d38c4',
                                'ContentType' => 'application/json',
                                'referenceId' => $orderData->referenceId, 'refundAmount' => $orderData->product_total_amount, 'refundNote' => 'full refund'
                            ),
                            CURLOPT_HTTPHEADER => array(
                                'Accept' => 'application/json',
                                'x-api-version' => '2021-05-21',
                                'x-client-id' => '165253d13ce80549d879dba25b352561',
                                'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
                            ),
                        ));

                        $response = curl_exec($curl);
                        // dd($response);
                        curl_close($curl);
                        $dataString = json_decode($response);
                        if ($dataString->status == "OK") {
                            $data['message'] = $dataString->message;
                            $data['refundId'] = $dataString->refundId;
                        } else {
                            $data['message'] = $dataString->message;
                            //  $data['message'] = "Refund failed! Please contact store";
                        }

                        if ($dataString->status == "OK") {
                            $orderData->refundId = $dataString->refundId;
                            $orderData->refundStatus = "Inprogress";
                            $orderData->isRefunded = 1;
                        }
                        // dd($dataString->message);

                        //echo $response;
                        //  die;



                        // $data = [
                        //     "refundAmount" => $orderData->product_total_amount,
                        //     "refundNote" => "Order amount refund",
                        //     "referenceId" => $orderData->referenceId,

                        // ];
                        // $dataString = json_encode($data);

                        // $headers = [
                        //     'appId: 165253d13ce80549d879dba25b352561',
                        //     'secretKey: bab0967cdc3e5559bded656346423baf0b1d38c4',
                        //     'ContentType: application/json'
                        // ];

                        // $ch = curl_init();

                        // curl_setopt($ch, CURLOPT_URL, 'https://api.cashfree.com/api/v1/order/refund');
                        // curl_setopt($ch, CURLOPT_POST, true);
                        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

                        // $response = curl_exec($ch);


                        // $curl = curl_init();

                        // curl_setopt_array($curl, array(
                        //     CURLOPT_URL => 'https://api.cashfree.com/api/v1/order/refund',
                        //     CURLOPT_RETURNTRANSFER => true,
                        //     CURLOPT_ENCODING => '',
                        //     CURLOPT_MAXREDIRS => 10,
                        //     CURLOPT_TIMEOUT => 0,
                        //     CURLOPT_FOLLOWLOCATION => true,
                        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        //     CURLOPT_CUSTOMREQUEST => 'POST',
                        //     CURLOPT_POSTFIELDS => array(,,, 'refundAmount' => '1', 'refundNote' => 'full refund'),
                        //     CURLOPT_HTTPHEADER => array(
                        //         'appId: 165253d13ce80549d879dba25b352561',
                        //         'secretKey: bab0967cdc3e5559bded656346423baf0b1d38c4',
                        //         'ContentType: application/json'
                        //     ),
                        // ));

                        // $response = curl_exec($curl);

                        // curl_close($curl);
                        //echo $response;

                        //die;
                    }

                    $orderData->status_id = 5;
                    if ($orderData->update()) {

                        $orderItemData = Trn_store_order_item::where('order_id', $order_id)->get();
                        foreach ($orderItemData as $o) {

                            $productVarOlddata = Mst_store_product_varient::find($o->product_varient_id);

                            $sd = new Mst_StockDetail;
                            $sd->store_id = $orderData->store_id;
                            $sd->product_id = $o->product_id;
                            $sd->stock = $o->quantity;
                            $sd->product_varient_id = $o->product_varient_id;
                            $sd->prev_stock = $productVarOlddata->stock_count;
                            $sd->save();

                            DB::table('mst_store_product_varients')->where('product_varient_id', $o->product_varient_id)->increment('stock_count', $o->quantity);
                        }



                        $storeDatas = Trn_StoreAdmin::where('store_id', $orderData->store_id)->where('role_id', 0)->first();
                        $storeDevice = Trn_StoreDeviceToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $orderData->store_id)->get();
                        $storeWeb = Trn_StoreWebToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $orderData->store_id)->get();

                        foreach ($storeDevice as $sd) {
                            $title = 'Order cancelled';
                            $body = 'Order cancelled by customer! Order Id: ' . $orderData->order_number;
                            $clickAction = "OrdersFragment";
                            $type = "order";
                            $data['response'] =  $this->storeNotification($sd->store_device_token, $title, $body,$clickAction,$type);
                        }

                        foreach ($storeWeb as $sw) {
                            
                            $title = 'Order cancelled';
                            $body = 'Order cancelled by customer! Order Id: ' . $orderData->order_number;
                            $clickAction = "OrdersFragment";
                            $type = "order";
                            $data['response'] =  Helper::storeNotifyWeb($sw->store_web_token, $title, $body,$type,$clickAction);
                        }


                        $data['status'] = 1;
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Order not found ";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Customer not found ";
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
    public function deliverySlotTest()
    {
        $slot=Trn_StoreDeliveryTimeSlot::find(56);
        $proDatas=Mst_store_product::whereIn('product_id',[394,395,396,397])->get();
        $currTime = date("G:i");
        $pua=0;
    foreach($proDatas as $proData)
    {
        $start = $proData->timeslot_start_time; //init the start time
        $end = $proData->timeslot_end_time; //init the end time
        //return $start;
       
        if ($proData->is_timeslot_based_product==1)
        {
            
            if($slot->time_start<$start && $slot->time_end>$end)
            {
                $pua=$pua+1;
            }
           
        }
    }

        return $pua;

    }
   
}
