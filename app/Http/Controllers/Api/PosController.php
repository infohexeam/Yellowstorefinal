<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
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

use App\Models\admin\Mst_StockDetail;
use App\Trn_pos_lock;

class PosController extends Controller
{
    public function listCustomers(Request $request)
    {
        $data = array();

        try {
            if ($data['customerDetails']  = Trn_store_customer::select('customer_id', 'customer_first_name', 'customer_last_name', 'customer_mobile_number')->orderBy('customer_id', 'DESC')->get()) {
                foreach ($data['customerDetails'] as $c) {
                    if (!isset($c->customer_last_name))
                        $c->customer_last_name = '';
                }
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
    public function lockProduct(Request $request)
    {
  
      $product_id = $request->product_id;
      $store_id=$request->store_id;
      $product_varient_id = $request->product_varient_id;
      $quantity=$request->quantity;
      $order_uid=$request->order_uid;
  
      //$products = DB::table('mst_store_products')->where('product_id', $product_id)->first();
      // $products = Mst_store_product::join('mst_store_product_varients', 'mst_store_product_varients.product_id', '=', 'mst_store_products.product_id')
      //   ->where('mst_store_products.product_id', '=', $product_id)
      //   ->where('mst_store_product_vlistInvarients.product_varient_id', '=', $product_varient_id)
      //   ->select(
      //     'mst_store_product_varients.product_varient_offer_price',
      //     'mst_store_product_varients.product_varient_id',
      //     'mst_store_product_varients.stock_count',
      //     'mst_store_product_varients.product_varient_price',
      //     'mst_store_product_varients.variant_name',
      //     'mst_store_products.*',
      //   )
      //   ->first();
      $pos_time=Trn_pos_lock::where('order_uid',$order_uid)->where('expiry_time','<=',Carbon::now()->toDateTimeString())->exists();
      if($pos_time)
      {
        $data['status'] = 3;
        $data['message'] = "POS Expired try again";
        
        return response()->json($data);
  
      }
  
        $productVarOlddata = Mst_store_product_varient::find($product_varient_id);
        $stockDiffernece=$productVarOlddata->stock_count-$quantity;
        if($stockDiffernece<0)
        {
            $data['status'] = 0;
            $data['message'] = "Out of stock now...Please try again later";
            
            return response()->json($data);
  
        }
        Mst_store_product_varient::where('product_varient_id', '=', $product_varient_id)->decrement('stock_count',$quantity);
      $lock=new Trn_pos_lock();
      $lock->order_number=$store_id.'-'.$order_uid;
      $lock->order_uid=$request->order_uid;
      $lock->product_varient_id=$product_varient_id;
      $lock->store_id=$store_id;
      $lock->ip_address=$request->ip();
      $lock->expiry_time=Carbon::now()->addMinutes(10);
      $lock->quantity=$quantity;
      $lock->save();
      $exp_update=Trn_pos_lock::where('order_uid',$request->order_uid)->first();
      $exp_update->expiry_time=$lock->expiry_time;
      $exp_update->update();
    
  
      // dd($products);
      $data['status']=1;
      $data['message']="sucessful";
      return response()->json($data);
    }
    public function removePosProduct(Request $request)
    {
      $data=array();
      $pvid=$request->product_varient_id;
      $order_uid=$request->order_uid;
      $pos_lock=Trn_pos_lock::where('product_varient_id',$pvid)->where('order_uid',$order_uid);
      if($pos_lock->first())
      {
        $qty=$pos_lock->first()->quantity;
        if($pos_lock->first()->delete())
        {
          Mst_store_product_varient::where('product_varient_id', '=', $pvid)->increment('stock_count', $qty);
        
          $data['status']=1;
          $data['message']="sucessful";
          return response()->json($data);
  
        }
        else
        {
          $data['status']=0;
          $data['message']="failure";
          return response()->json($data);
  
        }
       
  
  
       
      }
      else
      {
        $data['status']=0;
          $data['message']="failure-No lock exists";
          return response()->json($data);

      }
  
  
    }
    public function refreshPosProduct(Request $request)
    {
      $data=array();
      //$pvid=$request->product_varient_id;
      $order_uid=$request->order_uid;
      $pos_lock=Trn_pos_lock::where('order_uid',$order_uid);
      $locks=$pos_lock->get();
      if($locks)
      {
        
        foreach($locks as $lock)
        {
          $qty=$lock->quantity;
       
          Mst_store_product_varient::where('product_varient_id', '=',$lock->product_varient_id)->increment('stock_count',$qty); 

        }
        if($pos_lock->delete())
        {
            $data['status']=1;
            $data['message']="sucessful";
            return response()->json($data);

        }
        else
        {
            $data['status']=0;
            $data['message']="failed";
            return response()->json($data);
        }
      
        
       
  
  
       
      }
      else
      {
        $data['status']=0;
        $data['message']="failed";
        return response()->json($data);

      }

  
  
    }
    

    public function saveOrder(Request $request)
    {

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
                        'product_variants.*.discount_percentage'    => 'required',
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
                        'product_variants.*.discount_percentage.required'    => 'Discount percentage required',
                    ]
                );

                if (!$validator->fails()) {
                     $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();

                    $orderNumber = @$storeOrderCount + 1;

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


                    $store_order = new Trn_store_order;
                    $store_order->order_number = $orderNumberPrefix .substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4). @$orderNumber;
                    // $store_order->order_number = 'ORDRYSTR'.@$orderNumber;
                    $store_order->customer_id = 3;
                    $store_order->store_id =  $request->store_id;
                    if($request->store_admin_id != 0)
                    {
                        $store_order->store_admin_id =  $request->store_admin_id;
                    }
                    $store_order->subadmin_id =  $store_data->subadmin_id;
                    $store_order->product_total_amount =  $request->order_total_amount;
                    $store_order->payment_type_id = 1;
                    $store_order->payment_status = 9;
                    $store_order->status_id = 9;
                    $store_order->order_type = 'POS';

                    $store_order->save();
                    // $exist_last=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                    // $order_id = $exist_last->order_id;
                    $order_id = DB::getPdo()->lastInsertId();

                    $invoice_info['order_id'] = $order_id;
                    $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
                    $invoice_info['invoice_id'] = "INV0" . $order_id;
                    $invoice_info['created_at'] = Carbon::now();
                    $invoice_info['updated_at'] = Carbon::now();

                    Trn_order_invoice::insert($invoice_info);

                    foreach ($request->product_variants as $value) {
                        $productVarOlddata = Mst_store_product_varient::find($value['product_varient_id']);
                        if($productVarOlddata)
                        {
                            // $stockDiffernece=$productVarOlddata->stock_count-$value['quantity'];
                            // if($stockDiffernece<0)
                            // {
                               
                            //     $data['status'] =0;
                            //     $data['message'] = "Some products quantity is more than available stock..Try again.";
                            //     return response($data);
                  
                            // }
                        }
                        Mst_store_product_varient::where('product_varient_id', '=', $value['product_varient_id'])->decrement('stock_count', $value['quantity']);

                        if (!isset($value['discount_amount'])) {
                            $value['discount_amount'] = 0;
                        }

                        $negStock = -1 * abs($value['quantity']);

                        $sd = new Mst_StockDetail;
                        $sd->store_id = $request->store_id;
                        $sd->product_id = $value['product_id'];
                        $sd->stock = $negStock;
                        $sd->product_varient_id = $value['product_varient_id'];
                        $sd->prev_stock = $productVarOlddata->stock_count;
                        $sd->save();
                        $product_detail = Mst_store_product::where('product_id', '=',$productVarOlddata->product_id)->first();
                        $taxData = Mst_Tax::find($product_detail->tax_id);


                        $data2 = [
                            'order_id' => $order_id,
                            'product_id' => $value['product_id'],
                            'product_varient_id' => $value['product_varient_id'],
                            'customer_id' => 3,
                            'store_id' => $request['store_id'],
                            'quantity' => $value['quantity'],
                            'unit_price' =>  $value['unit_price'],
                            'total_amount' => $value['total_amount'],
                            'mrp'=>$productVarOlddata->product_varient_price,
                            'tax_amount' => $value['tax_amount'],
                            'tax_value'=>@$taxData->tax_value,
                            'tax_id'=>$taxData->tax_id,
                            'discount_amount' => $value['discount_amount'],
                            'discount_percentage' => $value['discount_percentage'],
                            'created_at'         => Carbon::now(),
                            'updated_at'         => Carbon::now(),
                        ];
                        Trn_store_order_item::insert($data2);
                    }

                    $data['status'] = 1;
                    $data['message'] = "Order saved.";
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
    public function saveOrderLock(Request $request)
    {

        try {
            $order_uid=$request->or_uid;
            $pos_time=Trn_pos_lock::where('order_uid',$order_uid)->where('expiry_time','<=',Carbon::now()->toDateTimeString())->exists();
            if($pos_time)
            {
              $data['status'] = 3;
              $data['message'] = "POS Expired...Try again";
              
              return response()->json($data);
        
            }

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
                        'product_variants.*.discount_percentage'    => 'required',
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
                        'product_variants.*.discount_percentage.required'    => 'Discount percentage required',
                    ]
                );

                if (!$validator->fails()) {
                     $storeOrderCount = Trn_store_order::where('store_id', $request->store_id)->count();

                    $orderNumber = @$storeOrderCount + 1;

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


                    $store_order = new Trn_store_order;
                    $store_order->order_number = $orderNumberPrefix .substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4). @$orderNumber;
                    // $store_order->order_number = 'ORDRYSTR'.@$orderNumber;
                    $store_order->customer_id = 3;
                    $store_order->store_id =  $request->store_id;
                    if($request->store_admin_id != 0)
                    {
                        $store_order->store_admin_id =  $request->store_admin_id;
                    }
                    $store_order->subadmin_id =  $store_data->subadmin_id;
                    $store_order->product_total_amount =  $request->order_total_amount;
                    $store_order->payment_type_id = 1;
                    $store_order->payment_status = 9;
                    $store_order->status_id = 9;
                    $store_order->order_type = 'POS';

                    $store_order->save();
                    // $exist_last=Trn_store_order::where('order_number',$orderNumberPrefix . @$orderNumber)->first();
                    // $order_id = $exist_last->order_id;
                    $order_id = DB::getPdo()->lastInsertId();

                    $invoice_info['order_id'] = $order_id;
                    $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
                    $invoice_info['invoice_id'] = "INV0" . $order_id;
                    $invoice_info['created_at'] = Carbon::now();
                    $invoice_info['updated_at'] = Carbon::now();

                    Trn_order_invoice::insert($invoice_info);

                    foreach ($request->product_variants as $value) {
                        $productVarOlddata = Mst_store_product_varient::find($value['product_varient_id']);
                       
                        //Mst_store_product_varient::where('product_varient_id', '=', $value['product_varient_id'])->decrement('stock_count', $value['quantity']);

                        if (!isset($value['discount_amount'])) {
                            $value['discount_amount'] = 0;
                        }

                        $negStock = -1 * abs($value['quantity']);

                        $sd = new Mst_StockDetail;
                        $sd->store_id = $request->store_id;
                        $sd->product_id = $value['product_id'];
                        $sd->stock = $negStock;
                        $sd->product_varient_id = $value['product_varient_id'];
                        $sd->prev_stock = $productVarOlddata->stock_count;
                        $sd->save();
                        $product_detail = Mst_store_product::where('product_id', '=',$productVarOlddata->product_id)->first();
                        $taxData = Mst_Tax::find($product_detail->tax_id);
                        Trn_pos_lock::where('product_varient_id',$value['product_varient_id'])->where('order_uid',$order_uid)->update(['status'=>0]);


                        $data2 = [
                            'order_id' => $order_id,
                            'product_id' => $value['product_id'],
                            'product_varient_id' => $value['product_varient_id'],
                            'customer_id' => 3,
                            'store_id' => $request['store_id'],
                            'quantity' => $value['quantity'],
                            'unit_price' =>  $value['unit_price'],
                            'total_amount' => $value['total_amount'],
                            'mrp'=>$productVarOlddata->product_varient_price,
                            'tax_amount' => $value['tax_amount'],
                            'tax_value'=>@$taxData->tax_value,
                            'tax_id'=>$taxData->tax_id,
                            'discount_amount' => $value['discount_amount'],
                            'discount_percentage' => $value['discount_percentage'],
                            'created_at'         => Carbon::now(),
                            'updated_at'         => Carbon::now(),
                        ];
                        Trn_store_order_item::insert($data2);
                    }

                    $data['status'] = 1;
                    $data['message'] = "Order saved.";
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

    public function listProducts(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {

                if ($data['productDetails']  = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                    // ->join('mst__taxes','mst_store_products.tax_id','=','mst__taxes.tax_id')
                    ->where('mst_store_products.store_id', $request->store_id)
                    //->where('mst_store_products.product_status', 1)
                    ->where('mst_store_products.product_type', 1)
                    ->where('mst_store_products.is_removed', 0)
                    ->where('mst_store_product_varients.is_removed', 0)
                    //->where('mst_store_product_varients.variant_status', 1)
                    ->where('mst_store_product_varients.stock_count', '>', 0)
                    ->orderBy('mst_store_products.product_id', 'DESC')
                    ->select(
                        'mst_store_products.product_id',
                        'mst_store_products.product_name',
                        'mst_store_products.product_code',
                        'mst_store_products.product_cat_id',
                        'mst_store_products.product_base_image',
                        'mst_store_products.product_status',
                        'mst_store_products.product_brand',
                        'mst_store_products.tax_id',
                        // 'mst__taxes.tax_name',
                        // 'mst__taxes.tax_value',
                        'mst_store_product_varients.product_varient_id',
                        'mst_store_product_varients.variant_name',
                        'mst_store_product_varients.product_varient_price',
                        'mst_store_product_varients.product_varient_offer_price',
                        'mst_store_product_varients.product_varient_offer_price',
                        'mst_store_product_varients.product_varient_base_image',
                        'mst_store_product_varients.is_base_variant',
                        'mst_store_product_varients.variant_status',
                        
                        'mst_store_product_varients.stock_count'
                    )->get()
                ) {
                    foreach ($data['productDetails'] as $product) {
                        if($product->product_status==0)
                       {
                           if($product->is_base_variant==1)
                           {
                               $product->variant_status="0";

                           }

                       }
               

                        $product->product_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_base_image;
                        
                        if($product->product_varient_base_image!=NULL)
                        {
                            $product->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_varient_base_image;

                        }
                        else
                        {
                            $product->product_varient_base_image = $product->product_base_image;

                        }
                        $taxData = Mst_Tax::find(@$product->tax_id);
                        $product->tax_name = @$taxData->tax_name;
                        $product->tax_value = @$taxData->tax_value;
               
                    }
                    // $new = array_filter($data['productDetails'], function ($var) {
                    //     return ($var['variant_status'] != 0);
                    // });
                    // $data['productDetails']=$new;
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
