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
use App\Models\admin\Trn_DeliveryBoyDeviceToken;

use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_business_types;

use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Trn_StoreAdmin;
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
use App\Models\admin\Sys_DeliveryStatus;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Mst_StockDetail;
use App\Models\admin\Sys_vehicle_type;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_CustomerDeviceToken;
use App\Models\admin\Trn_StoreDeliveryTimeSlot;


use App\Models\admin\Trn_customerAddress;
use App\Models\admin\Trn_DeliveryBoyLocation;



use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Trn_OrderSplitPayments;
use App\Models\admin\Trn_StoreDeviceToken;
use App\Models\admin\Trn_StoreWebToken;
use App\Trn_wallet_log;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function makeStoreCustomer(Request $request)
    {
        Trn_store_customer::where('customer_id', 3)->update(['customer_first_name' => 'Store Customer', 'customer_last_name' => null, 'customer_mobile_number' => '000000000']);
        echo "done";
    }
    
    public function listOrders(Request $request)
    {
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                $order_not_seen=Trn_store_order::where('store_id', '=', $store_id)->whereNull('TEST')->orderBy('order_id', 'DESC')->update(['TEST'=>1]);
                $query = Trn_store_order::select(
                    'order_id',
                    'order_number',
                    'delivery_address',
                    'created_at',
                    'status_id',
                    'customer_id',
                    'product_total_amount',
                    'order_type',
                    'isRefunded',
                    'refundStatus',
                    'refundId'
                )->where('store_id', $request->store_id);

                if (isset($request->order_number)) {
                    $query->where('order_number', 'LIKE', "%{$request->order_number}%");
                }
                if (isset($request->from) && isset($request->to)) {
                    $query->whereDate('created_at', '>=', $request->from)->whereDate('created_at', '<=', $request->to);
                }
                if (isset($request->from) && !isset($request->to)) {
                    $query->whereDate('created_at', '>=', $request->from);
                }
                if (!isset($request->from) && isset($request->to)) {
                    $query->whereDate('created_at', '<=', $request->to);
                }

                $perPage = 10;
                $page = $request->page ?? 1;
                $data['orderDetails'] = $query->orderBy('created_at', 'DESC')->paginate($perPage, ['*'], 'page', $page);

                foreach ($data['orderDetails'] as $order) {
                    $customerData = Trn_store_customer::find($order->customer_id);
                    if ($order->order_type == 'POS') {
                        $order->customer_name = 'Store Customer';
                    } else {
                        $cusAdd = Trn_customerAddress::find($order->delivery_address);
                        $order->customer_name = @$cusAdd->name;
                        if (!isset($cusAdd->name))
                            $order->customer_name = @$customerData->customer_first_name . " " . @$customerData->customer_last_name;
                    }

                    if (isset($order->status_id)) {
                        $statusData = Sys_store_order_status::find(@$order->status_id);
                        $order->status_name = @$statusData->status;
                    } else {
                        $order->status_name = null;
                    }

                    $order->order_date = Carbon::parse($order->created_at)->format('d-m-Y');
                    $order->invoice_link = url('get/invoice/' . Crypt::encryptString($order->order_id));
                    $order->item_list_link = url('item/list/' . Crypt::encryptString($order->order_id));
                }
                $data['status'] = 1;
                $data['message'] = "success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "Store not found";
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

    public function listOrders2(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                if ($query = Trn_store_order::select(
                    'order_id',
                    'order_number',
                    'delivery_address',
                    'created_at',
                    'status_id',
                    'customer_id',
                    'product_total_amount',
                    'order_type',
                    'isRefunded',
                    'refundStatus',
                    'refundId'
                )->where('store_id', $request->store_id)) {
                    if (isset($request->order_number)) {
                        $query->where('order_number', 'LIKE', "%{$request->order_number}%");
                    }
                    if (isset($request->from) && isset($request->to)) {
                        $query->whereDate('created_at', '>=', $request->from)->whereDate('created_at', '<=', $request->to);
                    }
                    if (isset($request->from) && !isset($request->to)) {
                        $query->whereDate('created_at', '>=', $request->from);
                    }
                    if (!isset($request->from) && isset($request->to)) {
                        $query->whereDate('created_at', '<=', $request->to);
                    }

                    if (isset($request->page)) {
                        $data['orderDetails'] = $query->orderBy('created_at', 'DESC')->paginate(10, ['data'], 'page', $request->page);
                    } else {
                        $data['orderDetails'] = $query->orderBy('created_at', 'DESC')->paginate(10);
                    }


                    foreach ($data['orderDetails'] as $order) {


                        $customerData = Trn_store_customer::find($order->customer_id);
                        if ($order->order_type == 'POS') {
                            $order->customer_name = 'Store Customer';
                        } else {
                            $cusAdd = Trn_customerAddress::find($order->delivery_address);
                            $order->customer_name = @$cusAdd->name;
                            if (!isset($cusAdd->name))
                                $order->customer_name = @$customerData->customer_first_name . " " . @$customerData->customer_last_name;
                        }



                        if (isset($order->status_id)) {
                            $statusData = Sys_store_order_status::find(@$order->status_id);
                            $order->status_name = @$statusData->status;
                        } else {
                            $order->status_name = null;
                        }
                        $order->order_date = Carbon::parse($order->created_at)->format('d-m-Y');
                        $order->invoice_link =  url('get/invoice/' . Crypt::encryptString($order->order_id));
                        $order->item_list_link = url('item/list/' . Crypt::encryptString($order->order_id));
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

    public function viewOrder(Request $request)
    {
        $data = array();

        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                
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
                    $store_id = $request->store_id;
                    $ord = Trn_store_order::Find($order_id);
                    $ord->TEST=1;
                    $ord->update();
                    // dd(Trn_store_order::select('order_id','time_slot','delivery_boy_id','order_note','payment_type_id','order_number','created_at','status_id','customer_id','product_total_amount')->where('order_id',$order_id)->where('store_id',$store_id)->first());

                    if ($data['orderDetails']  = Trn_store_order::select("*")->where('order_id', $order_id)->where('store_id', $store_id)->first()) {
                       
                        if (!isset($data['orderDetails']->order_note))
                            $data['orderDetails']->order_note = '';

                        if (!isset($data['orderDetails']->reward_points_used))
                            $data['orderDetails']->reward_points_used = "0";

                        if (!isset($data['orderDetails']->amount_reduced_by_rp))
                            $data['orderDetails']->amount_reduced_by_rp = "0";



                        if (!isset($data['orderDetails']->delivery_accept))
                            $data['orderDetails']->delivery_accept = '0';

                        if (isset($data['orderDetails']->customer_id)) {
                            $customerData = Trn_store_customer::find($data['orderDetails']->customer_id);
                            //    dd($customerData);
                            // $data['orderDetails']->customer_name = $customerData->customer_first_name." ".$customerData->customer_last_name;

                            $data['orderDetails']->customer_mobile = @$customerData->customer_mobile_number;

                            if ($data['orderDetails']->order_type == 'POS') {
                                $customerAddressData = Trn_customerAddress::where('customer_id', $data['orderDetails']->customer_id)->where('default_status', 1)->first();
                            } else {
                                $customerAddressData = Trn_customerAddress::find($data['orderDetails']->delivery_address);
                            }
                            

                            if ($data['orderDetails']->order_type == 'POS') {

                                $data['orderDetails']->customer_name = 'Store Customer';
                            } else {

                                if (isset($customerAddressData->name)) {
                                    $data['orderDetails']->customer_name = @$customerAddressData->name;
                                } else {
                                    $data['orderDetails']->customer_name = $customerData->customer_first_name . " " . $customerData->customer_last_name;
                                }
                            }
                            

                            if (isset($customerAddressData->phone))
                                $data['orderDetails']->customer_mobile = @$customerAddressData->phone;

                            if (isset($customerAddressData->place))
                                $data['orderDetails']->place = @$customerAddressData->place;
                            else
                                $data['orderDetails']->place =    '';

                            if (isset($customerAddressData->districtFunction->district_name))
                                $data['orderDetails']->district_name =   @$customerAddressData->districtFunction->district_name;
                            else
                                $data['orderDetails']->district_name =    '';

                            if (isset($customerAddressData->stateFunction->state_name))
                                $data['orderDetails']->state_name =     @$customerAddressData->stateFunction->state_name;
                            else
                                $data['orderDetails']->state_name =    '';

                            if (isset($customerAddressData->stateFunction->country->country_name))
                                $data['orderDetails']->country_name =     @$customerAddressData->stateFunction->country->country_name;
                            else
                                $data['orderDetails']->country_name =    '';

                            


                            if (isset($customerAddressData->address))
                                $data['orderDetails']->customer_address = @$customerAddressData->address;
                            else
                                $data['orderDetails']->customer_address = ' ';

                            if (isset($customerAddressData->longitude))
                                $data['orderDetails']->c_longitude = @$customerAddressData->longitude;
                            else
                                $data['orderDetails']->c_longitude = ' ';

                            if (isset($customerAddressData->latitude))
                                $data['orderDetails']->c_latitude = @$customerAddressData->latitude;
                            else
                                $data['orderDetails']->c_latitude = ' ';

                            if (isset($customerAddressData->place))
                                $data['orderDetails']->c_place = @$customerAddressData->place;
                            else
                                $data['orderDetails']->c_place = ' ';

                            if (isset($customerAddressData->pincode))
                                $data['orderDetails']->customer_pincode = @$customerAddressData->pincode;
                            else
                                $data['orderDetails']->customer_pincode = ' ';

                            if (isset($customerAddressData->place))
                                $data['orderDetails']->customer_place = @$customerAddressData->place;
                            else
                                $data['orderDetails']->customer_place = ' ';

                                
                            $deliveryBoy = Mst_delivery_boy::find($data['orderDetails']->delivery_boy_id);
                            if (isset($deliveryBoy->delivery_boy_name))
                                $data['orderDetails']->delivery_boy = @$deliveryBoy->delivery_boy_name;
                            else
                                $data['orderDetails']->delivery_boy = '';

                            if (isset($deliveryBoy->delivery_boy_mobile))
                                $data['orderDetails']->delivery_boy_mobile = @$deliveryBoy->delivery_boy_mobile;
                            else
                                $data['orderDetails']->delivery_boy_mobile = '';

                            $deliveryBoyLoc = Trn_DeliveryBoyLocation::where('delivery_boy_id', $data['orderDetails']->delivery_boy_id)
                                ->orderBy('dbl_id', 'DESC')->first();

                            if (isset($deliveryBoyLoc->latitude))
                                $data['orderDetails']->db_latitude = @$deliveryBoyLoc->latitude;
                            else
                                $data['orderDetails']->db_latitude = '';

                            if (isset($deliveryBoyLoc->longitude))
                                $data['orderDetails']->db_longitude = @$deliveryBoyLoc->longitude;
                            else
                                $data['orderDetails']->db_longitude = '';

                            

                            // $data['orderDetails']->db_latitude = @$deliveryBoyLoc->latitude;
                            // $data['orderDetails']->db_longitude = @$deliveryBoyLoc->longitude;

                            if ($data['orderDetails']->order_type == 'POS') {
                                $data['orderDetails']->customer_mobile = '';
                                $data['orderDetails']->customer_address = '';
                                $data['orderDetails']->customer_pincode = '';
                                $data['orderDetails']->customer_place = ' ';
                            }

                        } else {
                           
                            $data['orderDetails']->customer_name = '';
                            $data['orderDetails']->delivery_boy = '';
                            $data['orderDetails']->customer_mobile = '';
                            $data['orderDetails']->customer_address = '';
                            $data['orderDetails']->customer_pincode = '';
                            $data['orderDetails']->db_latitude = '';
                            $data['orderDetails']->db_longitude = '';
                            $data['orderDetails']->customer_place = ' ';
                        }

                        $storeData = Mst_store::find($request->store_id);
                        $data['orderDetails']->store_name = $storeData->store_name;

                        if (isset($storeData->gst))
                            $data['orderDetails']->gst = $storeData->gst;
                        else
                            $data['orderDetails']->gst = "";

                        $data['orderDetails']->store_primary_address = $storeData->store_primary_address;
                        $data['orderDetails']->store_mobile = $storeData->store_mobile;

                        if (isset($storeData->place))
                            $data['orderDetails']->place = $storeData->place;
                        else
                            $data['orderDetails']->place = '';

                        if (isset($storeData->place))
                            $data['orderDetails']->place = $storeData->place;
                        else
                            $data['orderDetails']->place = '';

                        if (isset($storeData->country->country_name))
                            $data['orderDetails']->country_name = $storeData->country->country_name;
                        else
                            $data['orderDetails']->country_name = '';

                        if (isset($storeData->state->state_name))
                            $data['orderDetails']->state_name = $storeData->state->state_name;
                        else
                            $data['orderDetails']->state_name = '';

                        if (isset($storeData->district->district_name))
                            $data['orderDetails']->district_name = $storeData->district->district_name;
                        else
                            $data['orderDetails']->district_name = '';

                        if (isset($storeData->town->town_name))
                            $data['orderDetails']->town_name = $storeData->town->town_name;
                        else
                            $data['orderDetails']->town_name = '';

                        

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
                        
                        if ($data['orderDetails']->order_type == 'POS' && $data['orderDetails']->store_admin_id != NULL) {

                        $data['orderDetails']->processed_by = $data['orderDetails']->storeadmin['admin_name'];
                        
                        }else{
                           
                            $data['orderDetails']->processed_by = "";
                        }
                        
                        

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
                        $data['orderDetails']->invoice_link =  url('get/invoice/' . Crypt::encryptString($data['orderDetails']->order_id));
                        $data['orderDetails']->item_list_link = url('item/list/' . Crypt::encryptString($data['orderDetails']->order_id));



                        $data['orderDetails']->orderItems = Trn_store_order_item::where('order_id', $data['orderDetails']->order_id)
                            ->select('product_id', 'product_varient_id', 'order_item_id', 'quantity', 'discount_amount', 'discount_percentage', 'total_amount','mrp', 'tax_amount','tax_id','tax_value', 'unit_price', 'tick_status','is_timeslot_product','time_start','time_end')
                            ->get();


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
                            if(@$baseProductDetail!=NULL)
                            {
                                if ((@$baseProductDetail->product_type == 2) && (@$baseProductDetail->service_type == 2)) {
                                    $isServiceOrder = 1;
                                }

                            }
                           



                            $value->product_base_image = '/assets/uploads/products/base_product/base_image/' . @$baseProductDetail->product_base_image;

                            

                            if (@$baseProductDetail->product_name != @$value->productDetail->variant_name)
                                $value->product_name = @$baseProductDetail->product_name . " " . @$value->productDetail->variant_name;
                            else
                                $value->product_name = @$baseProductDetail->product_name;

                            $taxFullData = Mst_Tax::find(@$value->tax_id);
                            //return $value->tax_id;
                            //return response($taxFullData);
                            //$taxFullData->tax_value=$value->tax_value;
                            // $gstAmount = $value['productDetail']->product_varient_offer_price * $baseProductDetail->tax_value / (100 + $baseProductDetail->tax_value);
                            // $orgCost = $value['productDetail']->product_varient_offer_price * 100 / (100 + $baseProductDetail->tax_value);

                            $discount_amount = (@$vaproductDetail->product_varient_price - @$vaproductDetail->product_varient_offer_price) * $value->quantity;
                            //$value->discount_amount =  number_format((float)$discount_amount, 2, '.', '');
                            $value->taxPercentage = $value->tax_value;
                            $tTax = $value->quantity * (@$value->unit_price * @$value->tax_value / (100 + @$value->tax_value));
                            $value->gstAmount = number_format((float)$tTax, 2, '.', '');
                            $orgCost =  $value->quantity * (@$value->unit_price * 100 / (100 + @$value->tax_value));
                            $value->orgCost = number_format((float)$orgCost, 2, '.', '');

                            $stax = 0;
                            // dd($splitdata);

                            

                            $splitdata = [];

                            if (isset($taxFullData)) {
                                $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id',@$value->tax_id)->get();

                                foreach ($splitdata as $sd) {
                                    if (@$value->tax_value == 0 || !isset($value->tax_value))
                                        $value->tax_value = 1;

                                    $stax = ($sd->split_tax_value * $tTax) / @$value->tax_value;
                                    $sd->tax_split_value = number_format((float)$stax, 2, '.', '');
                                }
                            }
                            

                            $value['taxSplitups']  = @$splitdata;
                        }

                        if ($isServiceOrder == 1) {
                            $data['orderDetails']->service_order = 1;
                        }

                        //  $tTax = $taxFullData->tax_value * $value->quantity;
                        // $value->total_amount = $value->total_amount  - $tTax;
                        // $value->tax_amount = $tTax;

                        // $value['productDetail']->product_varient_offer_price - $taxFullData->tax_value;





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
                                } else {
                                    $row->orderSplitPayments = [];
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



    public function activeDelievryBoysList(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                if ($data['deliveryBoysDetails'] = Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
                    ->select(
                        'mst_delivery_boys.delivery_boy_id',
                        'mst_delivery_boys.delivery_boy_name',
                        'mst_delivery_boys.delivery_boy_name',
                        'mst_delivery_boys.delivery_boy_name',
                        'mst_delivery_boys.delivery_boy_mobile'
                    )
                    ->where('mst_store_link_delivery_boys.store_id', $request->store_id)
                    ->where('mst_delivery_boys.availability_status', 1)
                    ->where('mst_delivery_boys.delivery_boy_status', 1)
                    ->whereNull('mst_delivery_boys.deleted_at')
                    ->get()
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


    public function listDeliveryBoys(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;
                if ($data['deliveryBoysDetails'] = Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
                    ->select(
                        'mst_delivery_boys.delivery_boy_id',
                        'mst_delivery_boys.delivery_boy_name',
                        'mst_delivery_boys.delivery_boy_name',
                        'mst_delivery_boys.delivery_boy_name',
                        'mst_delivery_boys.delivery_boy_mobile',
                        'mst_delivery_boys.is_added_by_store',
                        'mst_delivery_boys.delivery_boy_status'

                    )
                    ->where('mst_store_link_delivery_boys.store_id', $request->store_id)
                    //->where('mst_delivery_boys.delivery_boy_status', 1)
                    ->whereNull('deleted_at')
                    ->get()
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
            else {
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
    public function getVehicleTypes()
    {
        
        $data = array();
        try {
            $vehicle_types = Sys_vehicle_type::all();
            
             $data['vehicle_types']  =$vehicle_types; 
             $data['status']=1;
             $data['message']="Vehicle types fetched";
            return response($data);
           
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
    public function getDeliveryBoy(Request $request)
    {
        $data = array();
        try {
            $dbid=$request->delivery_boy_id;
            $delivery_boy =Mst_delivery_boy::Find($dbid);
            if($delivery_boy)
            {
                $data['details']  =$delivery_boy; 
                $data['status']=1;
                $data['message']="Details fetched";

            }
            else
            {
                $data['details']  =[]; 
                $data['status']=0;
                $data['message']="Details not fetched";

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
    public function storeDelivery_boy(Request $request, Mst_delivery_boy $delivery_boy)
{
    $data = array();
    try {
    $validator = Validator::make(
        $request->all(),
        [
            'delivery_boy_name'            => 'required',
            'delivery_boy_mobile'          => 'required|unique:mst_delivery_boys',
            'delivery_boy_address'         => 'required',
            'vehicle_number'               => 'required',
            'vehicle_type_id'              => 'required',
            'country_id'                   => 'required',
            'state_id'                     => 'required',
            'town_id'                      => 'required',
            'district_id'                  => 'required',
            'delivery_boy_commision'       => 'required|gte:0',
            'delivery_boy_commision_amount' => 'required|gte:0',
            'delivery_boy_username'        => 'required|unique:mst_delivery_boys',
            'delivery_boy_password'        => 'required|min:8|same:password_confirmation|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/u',
            'delivery_boy_image'           => 'mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=150,150|max:1024'
        ],
        [
            'delivery_boy_name.required'           => 'Delivery boy name required',
            'delivery_boy_mobile.required'         => 'Mobile required',
            'delivery_boy_address.required'        => 'Address required',
            'vehicle_number.required'              => 'Vehicle number required',
            'vehicle_type_id.required'             => 'Vehicle type required',
            'country_id.required'                  => 'Country required',
            'state_id.required'                    => 'State required',
            'town_id.required'                     => 'Town required',
            'district_id.required'                 => 'District  required',
            'delivery_boy_commision.required'      => 'Delivery boy commission percentage required',
            'delivery_boy_commision_amount.required' => 'Delivery boy commission amount required',
            'delivery_boy_username.required'        => 'Username required',
            'delivery_boy_password.required'        => 'Password required',
            'delivery_boy_password.min'            => 'Password must be at least 8 characters long',
            'delivery_boy_password.same'           => 'Passwords do not match',
            'delivery_boy_password.regex'          => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character',
            'delivery_boy_image.mimes'             => 'Invalid image format. Only JPEG, PNG, JPG, GIF, and SVG allowed',
            'delivery_boy_image.dimensions'        => 'Image dimensions are invalid. Minimum width and height should be 150 pixels.',
            'delivery_boy_image.max'               => 'Maximum image size allowed is 1024 KB'
        ]
    );

    if (!$validator->fails()) {
        $store_id = $request->store_id;
        $data = $request->except('_token');

        $delivery_boy->delivery_boy_name = $request->delivery_boy_name;
        $delivery_boy->delivery_boy_mobile = $request->delivery_boy_mobile;
        $delivery_boy->delivery_boy_email = $request->delivery_boy_email;
        $delivery_boy->delivery_boy_address = $request->delivery_boy_address;
        $delivery_boy->vehicle_number = $request->vehicle_number;
        $delivery_boy->vehicle_type_id = $request->vehicle_type_id;
        $delivery_boy->store_id = $store_id;
        $delivery_boy->country_id = $request->country_id;
        $delivery_boy->state_id = $request->state_id;
        $delivery_boy->district_id = $request->district_id;
        $delivery_boy->town_id = $request->town_id;
        $delivery_boy->is_added_by_store=1;

       

        $delivery_boy->delivery_boy_commision = $request->delivery_boy_commision ?? 0;
        $delivery_boy->delivery_boy_commision_amount = $request->delivery_boy_commision_amount ?? 0;
        $delivery_boy->delivery_boy_username = $request->delivery_boy_username;
        $delivery_boy->password  = Hash::make($request->delivery_boy_password);
        $delivery_boy->delivery_boy_status = 0;

        if ($request->hasFile('delivery_boy_image')) {
            $photo = $request->file('delivery_boy_image');
            $filename = time() . '.' . $photo->getClientOriginalExtension();
            $destinationPath = 'assets/uploads/delivery_boy/images';
            $thumb_img = Image::make($photo->getRealPath());
            $thumb_img->save($destinationPath . '/' . $filename, 80);
            $delivery_boy->delivery_boy_image = $filename;
        }

        $delivery_boy->save();

        if (isset($store_id)) {
            $last_insert_id = DB::getPdo()->lastInsertId();
            $date =  Carbon::now();
            $dataz = [
                'store_id' => $store_id,
                'delivery_boy_id' => $last_insert_id,
                'created_at' => $date,
                'updated_at' => $date,
            ];
            Mst_store_link_delivery_boy::insert($dataz);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Delivery boy added successfully.'
        ]);
    } else {
        return response()->json([
            'status' => 0,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ]);
    }
} catch (\Exception $e) {
    $response = ['status' => '0', 'message' => $e->getMessage()];
    return response($response);
} catch (\Throwable $e) {
    $response = ['status' => '0', 'message' => $e->getMessage()];
    return response($response);
}
}
public function updateDelivery_boy(Request $request)
{
    // Initialize an empty array to hold data
    $data = array();

    try {
        // Retrieve the delivery boy ID from the request
        $boy_Id = $request->delivery_boy_id;
        $delivery_boy_id=$boy_Id;

        // Find the delivery boy by ID
        $delivery_boy = Mst_delivery_boy::find($boy_Id);


        // Check if password fields are empty
        if($request->password==NULL || $request->password_confirmation==NULL )
        {
            // Validation rules when password fields are empty
            $val_rules = [
                'delivery_boy_name'            => 'required',
                'delivery_boy_mobile'          => 'required|unique:mst_delivery_boys,delivery_boy_mobile,' . $delivery_boy_id . ',delivery_boy_id',
                'delivery_boy_address'         => 'required',
                'vehicle_number'               => 'required',
                'vehicle_type_id'              => 'required',
                'country_id'                   => 'required',
                'state_id'                     => 'required',
                'town_id'                      => 'required',
                'district_id'                  => 'required',
                'delivery_boy_commision'       => 'required|gte:0',
                'delivery_boy_commision_amount' => 'required|gte:0',
                'delivery_boy_username'        => 'required|unique:mst_delivery_boys,delivery_boy_username,' . $delivery_boy_id . ',delivery_boy_id',
            ];

            // Custom error messages for validation rules
            $val_messages = [
                'delivery_boy_name.required'           => 'Delivery boy name required',
                'delivery_boy_mobile.required'         => 'Mobile required',
                'delivery_boy_address.required'        => 'Address required',
                'vehicle_number.required'              => 'Vehicle number required',
                'vehicle_type_id.required'             => 'Vehicle type required',
                'country_id.required'                  => 'Country required',
                'state_id.required'                    => 'State required',
                'town_id.required'                     => 'Town required',
                'district_id.required'                 => 'District  required',
                'delivery_boy_commision.required'      => 'Delivery boy commission percentage required',
                'delivery_boy_commision_amount.required' => 'Delivery boy commission amount required',
                'delivery_boy_username.required'        => 'Username required',
            ];
        }
        else
        {
            // Validation rules when password fields are provided
            $val_rules = [
                'delivery_boy_name'            => 'required',
                'delivery_boy_mobile'          => 'required|unique:mst_delivery_boys,delivery_boy_mobile,' . $delivery_boy_id . ',delivery_boy_id',
                'delivery_boy_address'         => 'required',
                'vehicle_number'               => 'required',
                'vehicle_type_id'              => 'required',
                'country_id'                   => 'required',
                'state_id'                     => 'required',
                'town_id'                      => 'required',
                'district_id'                  => 'required',
                'delivery_boy_commision'       => 'required|gte:0',
                'delivery_boy_commision_amount' => 'required|gte:0',
                'delivery_boy_username'        => 'required|unique:mst_delivery_boys,delivery_boy_username,' . $delivery_boy_id . ',delivery_boy_id',
                'password'  => 'sometimes|min:8|same:password_confirmation|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/u',
            ];

            // Custom error messages for validation rules when password fields are provided
            $val_messages = [
                'delivery_boy_name.required'           => 'Delivery boy name required',
                'delivery_boy_mobile.required'         => 'Mobile required',
                'delivery_boy_address.required'        => 'Address required',
                'vehicle_number.required'              => 'Vehicle number required',
                'vehicle_type_id.required'             => 'Vehicle type required',
                'country_id.required'                  => 'Country required',
                'state_id.required'                    => 'State required',
                'town_id.required'                     => 'Town required',
                'district_id.required'                 => 'District  required',
                'delivery_boy_commision.required'      => 'Delivery boy commission percentage required',
                'delivery_boy_commision_amount.required' => 'Delivery boy commission amount required',
                'delivery_boy_username.required'        => 'Username required',
                'password.required'                    => 'Password required',
                'password.regex'                        => 'Password must include at least one upper case letter, lower case letter, number, and special character'
            ];
        }

        // Create a validator instance
        $validator = Validator::make(
            $request->all(),
            $val_rules,
            $val_messages
        );

        // Check if validation passes
        if (!$validator->fails()) {
            // Extract request data excluding the CSRF token

            // Update the delivery boy's properties with values from the request
            $delivery_boy->delivery_boy_name       = $request->delivery_boy_name;
            $delivery_boy->delivery_boy_mobile     = $request->delivery_boy_mobile;
            $delivery_boy->delivery_boy_email      = $request->delivery_boy_email;
            $delivery_boy->delivery_boy_address    = $request->delivery_boy_address;
            $delivery_boy->vehicle_number          = $request->vehicle_number;
            $delivery_boy->vehicle_type_id         = $request->vehicle_type_id;
            $delivery_boy->country_id              = $request->country_id;
            $delivery_boy->state_id                = $request->state_id;
            $delivery_boy->district_id             = $request->district_id;
            $delivery_boy->town_id                 = $request->town_id;
            $delivery_boy->delivery_boy_commision  = $request->delivery_boy_commision ?? 0;
            $delivery_boy->delivery_boy_commision_amount = $request->delivery_boy_commision_amount ?? 0;
            $delivery_boy->delivery_boy_username   = $request->delivery_boy_username;
            
            // If a password is provided, update it
            if (isset($request->password)) {
                $delivery_boy->password = Hash::make($request->password);
            }

            // Handle uploaded image (if any)
            if ($request->hasFile('delivery_boy_image')) {
                // Handle image upload...
                $photo = $request->file('delivery_boy_image');
                $old_delivery_boy_image = 'assets/uploads/company/logos/' . $delivery_boy->delivery_boy_image;
                if (is_file($old_delivery_boy_image)) {
                    unlink($old_delivery_boy_image);
                }
                $filename = time() . '.' . $photo->getClientOriginalExtension();
                $destinationPath = 'assets/uploads/delivery_boy/images';
                $thumb_img = Image::make($photo->getRealPath());
                $thumb_img->save($destinationPath . '/' . $filename, 80);
                $delivery_boy->delivery_boy_image = $filename;
            }

            // Save the updated delivery boy object
            $delivery_boy->update();

            // Return a success JSON response
            return response()->json([
                'status'  => 1,
                'message' => 'Delivery boy updated successfully.'
            ]);
        } else {
            // If validation fails, return JSON response with validation errors
            return response()->json([
                'status'  => 0,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors()
            ]);
        }
    } catch (\Exception $e) {
        // Handle exceptions and return an error JSON response
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response()->json($response, 500);
    } catch (\Throwable $e) {
        // Handle throwables and return an error JSON response
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response()->json($response, 500);
    }
}
public function destroyDelivery_boy(Request $request)
	{
        try{
            $data=array();
            $delivery_boy_id=$request->delivery_boy_id;
            $delivery_boy=Mst_delivery_boy::find($delivery_boy_id);
            if($delivery_boy)
            {
                $delete = $delivery_boy->delete();
                $data['status']=1;
                $data['message']="Delivery boy deleted successfully";
                return response($data);

            }
            else
            {
                $data['status']=0;
                $data['message']="Delivery boy not exist";
                return response($data);

            }
            

        }catch (\Throwable $e) {
            // Handle throwables and return an error JSON response
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response()->json($response, 500);
        }
        

		
	}

    public function restoreDelivery_boy(Request $request)
	{

		
        try{
            $data=array();
            $store_id  = $request->store_id;
		    $delivery_boys = Mst_delivery_boy::onlyTrashed()->orderBy('delivery_boy_id', 'DESC')->where('store_id',$store_id)->where('is_added_by_store',1)->get();
           
            $data['status']=1;
            $data['trashed_delivery_boys']=$delivery_boys;
            $data['message']="Delivery boy Trash list fetched";
            return response($data);

          
            

        }catch (\Throwable $e) {
            // Handle throwables and return an error JSON response
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response()->json($response, 500);
        }

		
		

		
	}
    public function restoreDelivery_boySave(Request $request)
	{
        try
        {
        $data=array();
		$dbid=$request->delivery_boy_id;
        $db=Mst_delivery_boy::onlyTrashed()->find($dbid);
        if(!$db)
        {
            $data['status']=0;
            $data['message']="Delivery boy not fetched";
            return response($data);

        }
        $db->restore();
        $data['status']=1;
        $data['message']="Delivery boy restored";
        return response($data);
        }
        catch (\Throwable $e) {
            // Handle throwables and return an error JSON response
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response()->json($response, 500);
        }

         
	}
    public function changedBoyStatus(Request $request)
	{
        try
        {
            $data=array();
            $delivery_boy_id=$request->delivery_boy_id;

            $delivery_boy = Mst_delivery_boy::Find($delivery_boy_id);

            $status = $delivery_boy->delivery_boy_status;

            if ($status == 0) {
                $delivery_boy->delivery_boy_status  = 1;
            } else {

                $delivery_boy->delivery_boy_status  = 0;
            }
            $delivery_boy->update();

            $data['status']=1;
            $data['message']="Delivery boy status changed";
            return response($data);

        }
        catch (\Throwable $e) {
            // Handle throwables and return an error JSON response
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response()->json($response, 500);
        }
        

		
	}


    public function listOrderStatus(Request $request)
    {
        $data = array();
        try {

            if ($data['orderStatusDetails'] = Sys_store_order_status::select('status_id', 'status')->get()) {
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



    public function updateOrder(Request $request)
    {
        $data = array();

        //$od = Trn_store_order::find($request->order_id);

        //  $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $od->customer_id)->get();

        // foreach ($customerDevice as $cd) {
        //     $title = 'working';
        //     //  $body = 'First order points credited successully..';
        //     $body = "working body";
        //     $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body);
        // }

        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                if (Trn_store_order::find($request->order_id)) {
                    $od = Trn_store_order::find($request->order_id);
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'status_id'          => 'required',
                        ],
                        [
                            'status_id.required'        => 'Status not found',
                        ]
                    );

                    if (!$validator->fails()) {
                        $order_id = $request->order_id;
                        $store_id = $request->store_id;
                        if($od->status_id==1)
                        {
                          if(!in_array($request->status_id,[4,5]))
                          {
                            $data['status'] = 0;
                            $data['message'] = "Cannot update to this status before confirming the order";
                            return response($data);
                    
                          }
                        }
                        if($od->status_id==9)
                        {
                         
                            $data['status'] = 0;
                            $data['message'] = "Order is already delivered.Cannot proceed";
                            return response($data);
                    
                        }

                        if (isset($request->status_id))
                            $orderdata2['order_note'] = $request->order_note;


                        $orderdata2['status_id'] = $request->status_id;

                        if ($request->status_id == 7) {
                            $orderdata2['delivery_status_id'] = 1;
                        } else if ($request->status_id == 8) {
                            $orderdata2['delivery_status_id'] = 2;
                        } else if ($request->status_id == 9) {
                            $orderdata2['delivery_status_id'] = 3;
                        } 
                        else if ($request->status_id == 5) {
                            $orderdata2['delivery_status_id'] = 4;
                        } else {
                            $orderdata2['delivery_status_id'] = null;
                        }

                        if (($request->status_id == 9) && ($od->status_id != 9)) {
                            // $order->delivery_date = Carbon::now()->format('Y-m-d');
                            // $order->delivery_time = Carbon::now()->format('H:i');

                            $orderdata2['delivery_date'] = Carbon::now()->format('Y-m-d');
                            $orderdata2['delivery_time'] =  Carbon::now()->format('H:i');
                            $orderDataz = Trn_store_order::Find($order_id);

                            if ($orderDataz->order_type == 'APP') {
                                if($orderDataz->is_collect_from_store==NULL || $orderDataz->is_collect_from_store==0 )
                                {
                                if (($orderDataz->delivery_boy_id == 0) || !isset($orderDataz->delivery_boy_id)) {
                                    $data['status'] = 0;
                                    $data['message'] = "Delivery boy not assigned";
                                    return response($data);
                                }
                            }
                            }

                            // reward points 

                            $configPoint = Trn_configure_points::find(1);
                            $orderAmount  = $configPoint->order_amount;
                            $orderPoint  = $configPoint->order_points;

                            //$orderAmounttoPointPercentage =  $orderPoint / $orderAmount;
                            //$orderPointAmount =  $orderDataz->product_total_amount * $orderAmounttoPointPercentage;
                            $orderPointAmount=Helper::totalOrderCredit($orderAmount,$orderPoint,$orderDataz->product_total_amount);
                            //echo $orderPointAmount;die;
                            ///////////////////////////////////////////////////////
                            $store_id=$request->store_id;
                            $storeConfigPoint = Trn_configure_points::where('store_id',$store_id)->first();
                            if($storeConfigPoint)
                            {
                            $storeOrderAmount  = $storeConfigPoint->order_amount;
                            $storeOrderPoint  = $storeConfigPoint->order_points;

                            // $storeOrderAmounttoPointPercentage =  $storeOrderPoint / $storeOrderAmount;
                            // $storeOrderPointAmount =  $orderDataz->product_total_amount * $storeOrderAmounttoPointPercentage;
                            $storeOrderPointAmount=Helper::totalOrderCredit($storeOrderAmount,$storeOrderPoint,$orderDataz->product_total_amount);
                            }
                            ///////////////////////////////////////////////////////


                            /*if (Trn_store_order::where('customer_id', $orderDataz->customer_id)->count() == 1) {
                                $configPoint = Trn_configure_points::find(1);

                                // first - order - point
                                $refCusData = Trn_store_customer::find($orderDataz->customer_id);

                                $cr = new Trn_customer_reward;
                                $cr->transaction_type_id = 0;
                                $cr->reward_points_earned = $configPoint->first_order_points;
                                $cr->customer_id = $orderDataz->customer_id;
                                $cr->order_id = $orderDataz->order_id;
                                $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
                                $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                                $cr->reward_point_status = 1;
                                $cr->discription = "First order points";
                                if ($cr->save()) {
                                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $refCusData->referred_by)->get();

                                    foreach ($customerDevice as $cd) {
                                        $title = 'First order points credited';
                                        //  $body = 'First order points credited successully..';
                                        $body = $configPoint->first_order_points . ' points credited to your wallet..';
                                        $clickAction = "MyWalletFragment";
                                        $type = "wallet";
                                        $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                    }
                                }


                                // referal - point
                                $refCusData = Trn_store_customer::find($orderDataz->customer_id);
                                if ($refCusData->referred_by) {
                                    $crRef = new Trn_customer_reward;
                                    $crRef->transaction_type_id = 0;
                                    $crRef->reward_points_earned = $configPoint->referal_points;
                                    $crRef->customer_id = $refCusData->referred_by;
                                    $crRef->order_id = null;
                                    $crRef->reward_approved_date = Carbon::now()->format('Y-m-d');
                                    $crRef->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                                    $crRef->reward_point_status = 1;
                                    $crRef->discription = "Referal points";
                                    $crRef->save();

                                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $refCusData->referred_by)->get();

                                    foreach ($customerDevice as $cd) {
                                        $title = 'Referal points credited';
                                        $body = $configPoint->referal_points . ' points credited to your wallet..';
                                        $clickAction = "MyWalletFragment";
                                        $type = "wallet";
                                        $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                    }


                                    // joiner - point
                                    $crJoin = new Trn_customer_reward;
                                    $crJoin->transaction_type_id = 0;
                                    $crJoin->reward_points_earned = $configPoint->joiner_points;
                                    $crJoin->customer_id = $orderDataz->customer_id;
                                    $crJoin->order_id = $orderDataz->order_id;
                                    $crJoin->reward_approved_date = Carbon::now()->format('Y-m-d');
                                    $crJoin->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                                    $crJoin->reward_point_status = 1;
                                    $crJoin->discription = "Referal joiner points";
                                    if ($crJoin->save()) {
                                        $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $orderDataz->referred_by)->get();

                                        foreach ($customerDevice as $cd) {
                                            $title = 'Referal joiner points credited';
                                            //  $body = 'Referal joiner points credited successully..';
                                            $body = $configPoint->joiner_points . ' points credited to your wallet..';
                                            $clickAction = "MyWalletFragment";
                                            $type = "wallet";
                                            $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                        }
                                    }
                                }
                            }*/

                            //if (Trn_customer_reward::where('order_id', $orderDataz->order_id)->count() < 1) {


                                //if ((Trn_customer_reward::where('order_id', $orderDataz->order_id)->count() < 1) || (Trn_store_order::where('customer_id', $orderDataz->customer_id)->count() >= 1)) {
                                    if($orderPointAmount!=0.00)
                                    {
                                    $cr = new Trn_customer_reward;
                                    $cr->transaction_type_id = 0;
                                    $cr->reward_points_earned = $orderPointAmount;
                                    $cr->customer_id = $orderDataz->customer_id;
                                    $cr->order_id = $orderDataz->order_id;
                                    $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
                                    $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                                    $cr->reward_point_status = 1;
                                    $cr->discription = 'admin points';
                                    $cr->save();
                                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $orderDataz->customer_id)->get();
                                    foreach ($customerDevice as $cd) {
    
                                        $title = 'App Order Points Credited';
                                        $body = $orderPointAmount . ' points credited to your wallet';
                                        $clickAction = "MyWalletFragment";
                                        $type = "wallet";
                                        $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                    }

                                    }
                                    $storeDatas = Trn_StoreAdmin::where('store_id',$orderDataz->store_id)->where('role_id', 0)->first();
                                    $storeDevice = Trn_StoreDeviceToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id', $orderDataz->store_id)->get();
                                    
                                    if($storeConfigPoint)
                                    {
                                    if($storeOrderPointAmount!=0.00)
                                    {
                                    $scr = new Trn_customer_reward;
                                    $scr->transaction_type_id = 0;
                                    $scr->store_id=$store_id;
                                    $scr->reward_points_earned = $storeOrderPointAmount;
                                    $scr->customer_id = $orderDataz->customer_id;
                                    $scr->order_id = $orderDataz->order_id;
                                    $scr->reward_approved_date = Carbon::now()->format('Y-m-d');
                                    $scr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                                    $scr->reward_point_status = 1;
                                    $scr->discription = 'store points';
                                    $scr->save();
                                    

                                    $wallet_log=new Trn_wallet_log();
                                    $wallet_log->store_id=$orderDataz->store_id;
                                    $wallet_log->customer_id=$orderDataz->customer_id;
                                    $wallet_log->order_id=$orderDataz->order_id;
                                    $wallet_log->type='credit';
                                    $wallet_log->points_debited=null;
                                    $wallet_log->points_credited=$storeOrderPointAmount;
                                    $wallet_log->save();

                                    // foreach ($storeDevice as $sd) {
    
                                    //     $title = 'Store Points Credited';
                                    //     $body = $storeOrderPointAmount . ' points credited to your wallet';
                                    //     $clickAction = "MyWalletFragment";
                                    //     $type = "wallet";
                                    //     $data['response'] =  $this->storeNotification($sd->store_device_token, $title, $body,$clickAction,$type);
                                    // }
                                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $orderDataz->customer_id)->get();
                                    foreach ($customerDevice as $cd) {
    
                                        $title = 'Store Order Points Credited';
                                        $body = $storeOrderPointAmount . ' points credited to your store wallet';
                                        $clickAction = "MyWalletFragment";
                                        $type = "wallet";
                                        $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                    }

                                    }
                                    }
                                    
                                   
                                    foreach ($storeDevice as $sd) {
                                        $title = 'Order Delivered';
                                        $clickAction = "OrderListFragment";
                                        $body = 'Order delivered with order id ' . $orderDataz->order_number;
                                        $type = "order";
                                        $data['response'] =  $this->storeNotification($sd->store_device_token, $title, $body,$clickAction,$type);
                                    }


                                    $storeWeb = Trn_StoreWebToken::where('store_admin_id', $storeDatas->store_admin_id)->where('store_id',$orderDataz->store_id)->get();
                                    foreach ($storeWeb as $sw) {
                                        $title = 'Order Delivered';
                                        $body = 'Order delivered with order id ' . $orderDataz->order_number;
                                        $clickAction = "OrderListFragment";
                                        $type = "order";
                                        $data['response'] =  Helper::storeNotifyWeb($sw->store_web_token, $title, $body,$clickAction,$type);
                                    }
                                    
                                    
                            //$data['wallet_id']=$wallet_log->wallet_log_id;

                                    $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $orderDataz->customer_id)->get();
                                    if (($request->status_id == 9)) {
                                        $fop_store=Helper::checkFop($orderDataz);
                                        $fop_app=Helper::checkFopApp($orderDataz);
                                        $cust=Trn_store_customer::where('customer_id',$orderDataz->customer_id)->first();
                                        $str=Mst_store::where('store_id',$orderDataz->store_id)->first();
                                        // if($str->store_referral_id!=NULL)
                                        // {
                                        //   $st_uid=$str->store_referral_id;
                                        // }
                                        // else
                                        // {
                                        //   $st_uid=$str->store_id;
                            
                                        // }
                                        if($str)
                                        {
                                        if(is_null($str->store_referral_id))
                                        {
                                            $st_uid=$str->store_id;
                                           
                
                                        }
                                        else
                                        {
                                            $st_uid=$str->store_referral_id;
                                            
                
                                        }
                                        
                                        //dd($st_uid,1);
                                        //$ref_id=Helper::manageReferral($cust->referral_id,$st_uid,$orderDataz);
                                       
                                       
                                       
                                        }
                                        
                                        
                                        //$ref_id_app=Helper::manageAppReferral($cust->referral_id,$orderDataz);
                                      

                                       
                                              //if (Trn_store_order::where('customer_id', $customer_id)->count() == 1) {
                // $configPoint = Trn_configure_points::find(1);
      
                // $cr = new Trn_customer_reward;
                // $cr->transaction_type_id = 0;
                // $cr->reward_points_earned = $configPoint->first_order_points;
                // $cr->customer_id = $orderDataz->customer_id;
                // $cr->order_id = $order_id;
                // $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
                // $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                // $cr->reward_point_status = 1;
                // $cr->discription = "First order points";
                // $cr->save();
      
                // $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $orderDataz->customer_id)->get();
      
                // foreach ($customerDevice as $cd) {
                //   $title = 'First order points credited';
                //   //  $body = 'First order points credited successully..';
                //   $body = $configPoint->first_order_points . ' points credited to your wallet..';
                //   $clickAction = "OrderListFragment";
                //   $type = "order";
                //   $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                // }
      
      
                // // referal - point
                // // $refCusData = Trn_store_customer::find($order->customer_id);
                // // if ($refCusData->referred_by) {
                //   $crRef = new Trn_customer_reward;
                //   $crRef->transaction_type_id = 0;
                //   $crRef->reward_points_earned = $configPoint->referal_points;
                //   $crRef->customer_id = $ref_id;
                //   $crRef->order_id = $order_id;
                //   $crRef->reward_approved_date = Carbon::now()->format('Y-m-d');
                //   $crRef->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                //   $crRef->reward_point_status = 1;
                //   $crRef->discription = "Referal points";
                //   $crRef->save();
                //   $cst=Trn_store_customer::where('customer_id',$orderDataz->customer_id)->first();
                //   $cst->referred_by=$ref_id;
                //   $cst->update();
      
                //   $customerDevice = Trn_CustomerDeviceToken::where('customer_id',Helper::manageReferral($cust->referral_id,$st_uid,$orderDataz))->get();
      
                //   foreach ($customerDevice as $cd) {
                //     $title = 'Referal points credited';
                //     //$body = 'Referal points credited successully..';
                //     $body = $configPoint->referal_points . ' points credited to your wallet..';
                //     $clickAction = "OrderListFragment";
                //     $type = "order";
                //     $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                //   }
      
      
      
                //   // joiner - point
                //   $crJoin = new Trn_customer_reward;
                //   $crJoin->transaction_type_id = 0;
                //   $crJoin->reward_points_earned = $configPoint->joiner_points;
                //   $crJoin->customer_id = $orderDataz->customer_id;
                //   $crJoin->order_id = $orderDataz->order_id;
                //   $crJoin->reward_approved_date = Carbon::now()->format('Y-m-d');
                //   $crJoin->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                //   $crJoin->reward_point_status = 1;
                //   $crJoin->discription = "Referal joiner points";
                //   if ($crJoin->save()) {
                //     $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $orderDataz->customer_id)->get();
      
                //     foreach ($customerDevice as $cd) {
                //       $title = 'Referal joiner points credited';
                //       //$body = 'Referal joiner points credited successully..';
                //       $body = $configPoint->joiner_points . ' points credited to your wallet..';
                //       $clickAction = "OrderListFragment";
                //       $type = "order";
                //       $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                //     }
                //   }
                //}
              //}
                                          
                                        //}
                                        }
                                    foreach ($customerDevice as $cd) {
                                        if($od->payment_type_id==2)
                                    {
                                        $title = 'Order points credited';
                                        $body = $orderPointAmount . ' points credited to your wallet..';
                                        $clickAction = "MyWalletFragment";
                                        $type = "wallet";
                                        $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                        if($storeConfigPoint)
                                        {
                                        $title = 'Store order points credited';
                                        $body = @$storeOrderPointAmount . ' points credited to your store wallet..';
                                        $clickAction = "MyWalletFragment";
                                        $type = "wallet";
                                        $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                        }
                                    }
                                }
                               // }
                            //}

                            // echo $orderPointAmount;die;


                        }

                        if ($request->status_id == 4) { //confirm
                            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $od->customer_id)->get();

                            foreach ($customerDevice as $cd) {
                                $title = 'Order confirmed';
                                $body = "Your order " . $od->order_number . ' is confirmed..';
                                $clickAction = "OrderListFragment";
                                $type = "order";
                                $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                            }
                        }

                        if ($request->status_id == 6) { //picking complede
                            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $od->customer_id)->get();

                            foreach ($customerDevice as $cd) {
                                $title = 'Order picking completed';
                                $body = "Your order " . $od->order_number . ' picking completed..';
                                $clickAction = "OrderListFragment";
                                $type = "order";
                                $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                            }
                        }
                        if ($request->status_id == 7) { //ready for delivery
                            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $od->customer_id)->get();

                            foreach ($customerDevice as $cd) {
                                $title = 'Order ready for delivery';
                                $body = "Your order " . $od->order_number . ' is ready for delivery..';
                                $clickAction = "OrderListFragment";
                                $type = "order";
                                $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                            }
                        }

                        if ($request->status_id == 8) { //out for delivery
                            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $od->customer_id)->get();

                            foreach ($customerDevice as $cd) {
                                $title = 'Order out for delivery';
                                $body = "Your order " . $od->order_number . ' is out for delivery..';
                                $clickAction = "OrderListFragment";
                                $type = "order";
                                $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                            }
                        }

                        if (($request->status_id == 9) && ($od->status_id != 9)) { // delivered
                            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $od->customer_id)->get();

                            foreach ($customerDevice as $cd) {
                                $title = 'Order delivered';
                                $body = "Your order " . $od->order_number . ' is deliverd..';
                                $clickAction = "OrderListFragment";
                                $type = "order";
                                $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                            }
                        }

                      


                        $orderdata2['delivery_boy_id'] = $request->delivery_boy_id;
                        if ($request->status_id == 7) {
                            if ($od->delivery_accept == null) {

                                $dBoyDevices = Trn_DeliveryBoyDeviceToken::where('delivery_boy_id', $request->delivery_boy_id)->get();

                                foreach ($dBoyDevices as $cd) {
                                    $title = 'Order Assigned';
                                    $body = 'New order(' . $od->order_number . ') arrived';
                                    $clickAction = "AssignedOrderFragment";
                                    $type = "order-assigned";
                                    $data['response'] =  Helper::deliveryBoyNotification($cd->dboy_device_token, $title, $body,$clickAction,$type);
                                }
                            }
                            $orderdata2['delivery_accept'] = null;
                        }

                        if ($request->status_id == 5) {
                            if($od->status_id==8)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Order is already out for delivery.You cannot cancel this order ";
                                return response($data);
        
                            }
                if($od->reward_points_used_store!=NULL||$od->reward_points_used_store!=0.00)
                    {
                        $scr = new Trn_customer_reward;
                        $scr->transaction_type_id = 0;
                        $scr->store_id=$od->store_id;
                        $scr->reward_points_earned = $od->reward_points_used_store;
                        $scr->customer_id = $od->customer_id;
                        $scr->order_id = $od->order_id;
                        $scr->reward_approved_date = Carbon::now()->format('Y-m-d');
                        $scr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
                        $scr->reward_point_status = 1;
                        $scr->discription = 'store points';
                        $scr->save();
                        

                        $wallet_log=new Trn_wallet_log();
                        $wallet_log->store_id=$od->store_id;
                        $wallet_log->customer_id=$od->customer_id;
                        $wallet_log->order_id=$od->order_id;
                        $wallet_log->type='credit';
                        $wallet_log->points_debited=null;
                        $wallet_log->points_credited=$od->reward_points_used_store;
                        $wallet_log->save();
                        

                    }
                            $dBoyDevices = Trn_DeliveryBoyDeviceToken::where('delivery_boy_id', $request->delivery_boy_id)->get();
    
                                foreach ($dBoyDevices as $cd) {
                                    $title = 'Order Cancelled';
                                    $body = 'An order(' . $od->order_number . ') has been cancelled';
                                    $clickAction = "AssignedOrderFragment";
                                    $type = "order";
                                    $data['response'] =  Helper::deliveryBoyNotification($cd->dboy_device_token, $title, $body,$clickAction,$type);
                                }
                                $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $od->customer_id)->get();
    
                                foreach ($customerDevice as $cd) {
                                    $title = 'Order Cancelled';
                                    $body = "Your order " . $od->order_number . ' has been cancelled..';
                                    $clickAction = "OrderListFragment";
                                    $type = "order";
                                    $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
                                }
                           

                            if (isset($od->referenceId) && ($od->isRefunded < 2)) {


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
                                        'referenceId' => $od->referenceId, 'refundAmount' => $od->product_total_amount, 'refundNote' => 'full refund'
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
                                    $orderdata2['refundId'] = $dataString->refundId;
                                    $orderdata2['refundStatus'] = "Inprogress";
                                    $orderdata2['isRefunded'] = 1;
                                }
                            }

                   
                            $orderData = Trn_store_order_item::where('order_id', $order_id)->get();
                            //dd($orderData);
                            if($od->status_id!=5)
                            {
                            
                            foreach ($orderData as $o) {

                                $productVarOlddata = Mst_store_product_varient::find($o->product_varient_id);

                                $sd = new Mst_StockDetail;
                                $sd->store_id = $request->store_id;
                                $sd->product_id = $o->product_id;
                                $sd->stock = $o->quantity;
                                $sd->product_varient_id = $o->product_varient_id;
                                $sd->prev_stock = $productVarOlddata->stock_count;
                                $sd->save();

                                DB::table('mst_store_product_varients')->where('product_varient_id', $o->product_varient_id)->increment('stock_count', $o->quantity);
                            }
                        }
                            
                            
                            foreach ($request->tickStatus as $key => $val) {
                                $tickStatus['tick_status'] = $val['tick_status'];
                                Trn_store_order_item::where('order_item_id', $val['order_item_id'])->update($tickStatus);
                            }
    
                        }
                        Trn_store_order::where('order_id', $order_id)->update($orderdata2);

                       

                        if (isset($request->delivery_boy_id)) {
                            $db=Mst_delivery_boy::where('delivery_boy_id',$request->delivery_boy_id)->first();
                            $orderData = [
                                'order_id'      => $order_id,
                                'delivery_boy_id' => $request->delivery_boy_id,
                                'commision_per_month'=>$db->delivery_boy_commision??0,
                                'commision_per_order'=>$db->delivery_boy_commision_amount??0,
                                'created_at'         => Carbon::now(),
                                'updated_at'         => Carbon::now(),
                            ];

                            Mst_order_link_delivery_boy::insert($orderData);
                        }




                        $data['status'] = 1;
                        $data['message'] = "Order updated";
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                        $data['errors'] = $validator->errors();
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

    public function listDeliveryBoysByStatus(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'work_status'          => 'required',
                    ],
                    [
                        'work_status.required'        => 'Work status required',
                    ]
                );

                if (!$validator->fails()) {
                    $work_status = $request->work_status;
                    $store_id = $request->store_id;
                    $dboy = array();


                    $delivery_boys1 = Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
                        ->where('mst_store_link_delivery_boys.store_id', $store_id)
                        ->pluck('mst_delivery_boys.delivery_boy_id')
                        ->toArray();


                    if ($work_status == 1) {
                        $assigned_delivery_boys = Trn_store_order::whereIn('delivery_boy_id', $delivery_boys1)
                            ->where('store_id', $store_id)
                            ->where('status_id', 7)
                            ->where('delivery_status_id', $work_status)
                            ->orderBy('order_id', 'DESC')
                            ->get();
                    } else if ($work_status == 2) {
                        $assigned_delivery_boys = Trn_store_order::whereIn('delivery_boy_id', $delivery_boys1)
                            ->where('store_id', $store_id)
                            ->where('status_id', 8)
                            ->where('delivery_status_id', $work_status)
                            ->orderBy('order_id', 'DESC')
                            ->get();
                    } else if ($work_status == 3) {
                        $assigned_delivery_boys = Trn_store_order::whereIn('delivery_boy_id', $delivery_boys1)
                            ->where('store_id', $store_id)
                            ->where('status_id', 9)
                            ->where('delivery_status_id', $work_status)
                            ->orderBy('order_id', 'DESC')
                            ->get();
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "work status not exist";
                        return response($data);
                    }



                    foreach ($assigned_delivery_boys as $ab) {
                        $custData = Trn_store_customer::find(@$ab->customer_id);
                        $ab->customer = @$custData->customer_first_name . " " . @$custData->customer_last_name;
                        $ab->orderDate = \Carbon\Carbon::parse($ab->created_at)->format('d-m-Y');


                        $deliveryBoy = \DB::table('mst_delivery_boys')
                            ->select('town_id', 'delivery_boy_id', 'delivery_boy_name', 'delivery_boy_mobile')
                            ->where('delivery_boy_id', @$ab->delivery_boy_id)
                            ->first();

                        $ab->town_id = @$deliveryBoy->town_id;
                        $ab->delivery_boy_id = @$deliveryBoy->delivery_boy_id;
                        $ab->delivery_boy_name = @$deliveryBoy->delivery_boy_name;
                        $ab->delivery_boy_mobile = @$deliveryBoy->delivery_boy_mobile;
                    }

                    $data['deliveryBoyDetails'] = $assigned_delivery_boys;


                    //   if($did = Mst_store_link_delivery_boy::join('mst_delivery_boys','mst_delivery_boys.delivery_boy_id','=','mst_store_link_delivery_boys.delivery_boy_id')
                    // ->select('mst_delivery_boys.delivery_boy_id',
                    // 'mst_delivery_boys.delivery_boy_name',
                    // 'mst_delivery_boys.delivery_boy_name',
                    // 'mst_delivery_boys.delivery_boy_name',
                    // 'mst_delivery_boys.delivery_boy_mobile')
                    // ->where('mst_store_link_delivery_boys.store_id',$request->store_id)
                    // ->get())
                    // {

                    //   foreach($did as $value)
                    //   {
                    //     if($orderData = Trn_store_order::
                    //     where('delivery_boy_id',$value->delivery_boy_id)
                    //     // ->where('payment_type_id',2)
                    //     ->where('store_id',$request->store_id)
                    //     ->where('delivery_status_id',$work_status)
                    //     ->orderBy('delivery_boy_id','DESC')->first())
                    //     {
                    //         $custData = Trn_store_customer::find($orderData->customer_id);
                    //         $value->order_id = $orderData->order_id;
                    //         $value->order_number = $orderData->order_number;
                    //         $value->order_date = Carbon::parse($orderData->created_at)->format('d-m-Y');
                    //          $value->customer = @$custData->customer_first_name." ".@$custData->customer_last_name;
                    //         $dboy[] = $value;
                    //     }


                    //     //  $value->orderData = Mst_order_link_delivery_boy::
                    //     //  join('trn_store_orders','trn_store_orders.order_id','=','mst_order_link_delivery_boys.order_id')
                    //     //  ->where('mst_order_link_delivery_boys.delivery_boy_id',$value->delivery_boy_id)
                    //     //  ->where('mst_order_link_delivery_boys.delivery_status_id',$work_status)
                    //     //  ->select('trn_store_orders.order_id',
                    //     //  'mst_order_link_delivery_boys.delivery_boy_id',
                    //     //  'mst_order_link_delivery_boys.delivery_status_id',
                    //     //  'trn_store_orders.order_number',
                    //     //  'trn_store_orders.customer_id'

                    //     //  )
                    //     //  ->first();

                    //     //   $customerData = Trn_store_customer::where('customer_id',@$value->orderData->customer_id)
                    //     //   ->select('customer_id','customer_first_name','customer_last_name','customer_mobile_number')
                    //     //   ->first();

                    //     //   $value->customerData = $customerData;

                    //   }

                    // $data['deliveryBoyDetails'] = $dboy;

                    //     $data['status'] = 1;
                    //     $data['message'] = "success";
                    //   // echo '<pre>';
                    //   // print_r($data);die;
                    //     return response($data);
                    // }
                    // else
                    // {
                    //     $data['status'] = 0;
                    //     $data['message'] = "failed";
                    //     return response($data);
                    // }
                    $data['status'] = 1;
                    $data['message'] = "success";
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


    public function listDeliveryStatus(Request $request)
    {
        $data = array();
        try {

            $data['deiveryStatusList'] = Sys_DeliveryStatus::select('delivery_status_id', 'delivery_status')->get();
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];

            return response($response);
        }
    }

    public function orderInvoice(Request $request)
    {
        $data = array();
        try {
            if (isset($request->order_id) && Trn_store_order::find($request->order_id)) {
                $order_id = $request->order_id;
                $order = Trn_store_order::where('order_id', $order_id)
                    ->select(
                        'order_id',
                        'order_number',
                        'customer_id',
                        'store_id',
                        'product_total_amount',
                        'payment_type_id',
                        'status_id',
                        'order_note',
                        'created_at'
                    )
                    ->first()->toArray();
                $customer = Trn_store_customer::where('customer_id', $order['customer_id'])
                    ->select(
                        'customer_id',
                        'customer_first_name',
                        'customer_last_name',
                        'customer_email',
                        'customer_mobile_number',
                        'customer_address',
                        'customer_location',
                        'customer_pincode',
                        'country_id',
                        'state_id'
                    )
                    ->first()->toArray();
                $status = Sys_store_order_status::find($order['status_id']);
                $order_items = Trn_store_order_item::where('order_id', $order_id)->get()->toArray();
                $store_data = Mst_store::where('store_id', $order['store_id'])->first()->toArray();
                $order['customerDetails'] = $customer;
                $order['orderStatus'] = $status;
                $order['orderItems'] = $order_items;
                $order['store_data'] = $store_data;
                // array_push($order, $customer);

                $data['orderDetails'] = $order;
                $data['status'] = 1;
                $data['message'] = "success";
                return response($data);
            } else {
                $data['status'] = 0;
                $data['message'] = "Order not found ";
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

    public function assignDeliveryBoy(Request $request)
    {
        $data = array();
        try {
            if (isset($request->order_id) && Trn_store_order::find($request->order_id)) {
                $order_id = $request->order_id;
                $validator = Validator::make(
                    $request->all(),
                    [
                        'delivery_boy_id'          => 'required',
                    ],
                    [
                        'delivery_boy_id.required'        => 'Delivery boy not found',
                    ]
                );

                if (!$validator->fails()) {
                    $delivery_boy_id = $request->delivery_boy_id;

                    if (Trn_store_order::where('order_id', $order_id)->update(['delivery_boy_id' => $delivery_boy_id, 'delivery_accept' => null])) {
                        $data['status'] = 1;
                        $data['message'] = "Assigned";
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
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
                $data['message'] = "Order not found ";
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
public function totalOrderCredit($configOrderAmount,$OrderTotal)
{
    $orderAmount  = $configOrderAmount;
    $productTotal=$OrderTotal;
    $amountRatio=$productTotal/$orderAmount;
    $orderPoint  = 10;
    $n=floor($amountRatio);
    return $n*$orderPoint;
    
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
}

