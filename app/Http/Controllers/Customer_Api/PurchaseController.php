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
use App\Models\admin\Trn_store_order;

use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_points_redeemed;

class PurchaseController extends Controller
{



    public function reduceRewardPoint(Request $request)
    {
        $data = array();
        try {
            if (isset($request->order_amount)) {
                if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                    $customer_id = $request->customer_id;

                    $totalCustomerRewardsCount = Trn_customer_reward::where('customer_id', $request->customer_id)->where('reward_point_status', 1)->sum('reward_points_earned');
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');

                    $customerRewardPoint = ($totalCustomerRewardsCount - $totalusedPoints) - $redeemedPoints;

                    //echo $customerRewardPoint;die;

                    if ($customerRewardPoint > 0) {


                        $ConfigPoints = Trn_configure_points::first();
                        $pointToRupeeRatio =   $ConfigPoints->rupee / $ConfigPoints->rupee_points; // points to rupee ratio

                        $avilableRewardAmount = $pointToRupeeRatio * $customerRewardPoint;
                        $maxRedeemAmountPerOrder = $ConfigPoints->max_redeem_amount;
                        $totalReducableAmount = ($avilableRewardAmount * $ConfigPoints->redeem_percentage) / 100; // 10% of order amount

                        if ($totalReducableAmount > $maxRedeemAmountPerOrder) {

                            $orderAmount = $request->order_amount;
                            $reducedOrderAmount = $orderAmount - $maxRedeemAmountPerOrder;
                            $customerUsedRewardPoint = $maxRedeemAmountPerOrder / $pointToRupeeRatio;
                            if ($reducedOrderAmount < 0) {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed";
                                return response($data);
                            }

                            $data['orderAmount'] = number_format((float)$orderAmount, 2, '.', '');
                            $data['totalReducableAmount'] = number_format((float)$maxRedeemAmountPerOrder, 2, '.', '');
                            $data['reducedOrderAmount'] = number_format((float)$reducedOrderAmount, 2, '.', '');
                            $data['reducedAmountByWalletPoints'] = number_format((float)$maxRedeemAmountPerOrder, 2, '.', '');
                            $data['usedPoint'] = number_format((float)$customerUsedRewardPoint, 2, '.', '');
                            $data['balancePoint'] = $customerRewardPoint - $customerUsedRewardPoint;
                        } else {

                            $orderAmount = $request->order_amount;
                            $reducedOrderAmount = $orderAmount - $totalReducableAmount;
                            $customerUsedRewardPoint = $totalReducableAmount / $pointToRupeeRatio;
                            if ($reducedOrderAmount < 0) {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed";
                                return response($data);
                            }
                            $data['orderAmount'] = number_format((float)$orderAmount, 2, '.', '');
                            $data['totalReducableAmount'] = number_format((float)$totalReducableAmount, 2, '.', '');
                            $data['reducedOrderAmount'] = number_format((float)$reducedOrderAmount, 2, '.', '');
                            $data['reducedAmountByWalletPoints'] = number_format((float)$totalReducableAmount, 2, '.', '');
                            $data['usedPoint'] = number_format((float)$customerUsedRewardPoint, 2, '.', '');
                            $data['balancePoint'] = $customerRewardPoint - $customerUsedRewardPoint;
                        }





                        //     $orderAmount = $request->order_amount;
                        //     $totalReducableAmount = ($orderAmount * $ConfigPoints->redeem_percentage) / 100; // 10% of order amount
                        //     $amountCanBeReduced = $pointToRupeeRatio * $customerRewardPoint;
                        //     if($totalReducableAmount >= $amountCanBeReduced){
                        //         $reducedOrderAmount = $orderAmount - $amountCanBeReduced;
                        //         $data['orderAmount'] = number_format((float)$orderAmount, 2, '.', '');
                        //         $data['totalReducableAmount'] = number_format((float)$totalReducableAmount, 2, '.', '');
                        //         $data['reducedOrderAmount'] = number_format((float)$reducedOrderAmount, 2, '.', '');
                        //         $data['reducedAmountByWalletPoints'] = number_format((float)$amountCanBeReduced, 2, '.', '');
                        //         $data['usedPoint'] = number_format((float)$customerRewardPoint, 2, '.', '');
                        //         $data['balancePoint'] = 0;
                        //     }else{
                        //         $usedPoint = $totalReducableAmount / $pointToRupeeRatio;
                        //         $reducedOrderAmount = $orderAmount - $totalReducableAmount;
                        //         $balancePoint = $customerRewardPoint - $usedPoint;
                        //         $data['orderAmount'] = number_format((float)$orderAmount, 2, '.', '');
                        //         $data['totalReducableAmount'] = number_format((float)$totalReducableAmount, 2, '.', '');
                        //         $data['reducedOrderAmount'] = number_format((float)$reducedOrderAmount, 2, '.', '');
                        //         $data['reducedAmountByWalletPoints'] = number_format((float)$totalReducableAmount, 2, '.', '');
                        //         $data['usedPoint'] = number_format((float)$usedPoint, 2, '.', '');
                        //         $data['balancePoint'] = number_format((float)$balancePoint, 2, '.', '');
                        //     }

                        $data['status'] = 1;
                        $data['message'] = "success";
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "No reward points available";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Customer not found";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Order amount required";
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



    public function addToCart(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                if (isset($request->product_varient_id) && Mst_store_product_varient::find($request->product_varient_id)) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'quantity' => 'required|numeric',
                            'product_varient_id' => 'required',
                            'customer_id' => 'required',
                            // 'store_id' => 'required' //to be added
                        ],
                        [
                            'quantity.required' => "Quantity required",
                            'product_varient_id.required'                => 'Product required',
                            'customer_id.required'                => 'Customer required',
                        ]
                    );
                    if (!$validator->fails()) {
                        
                            $varProdu = Mst_store_product_varient::find($request->product_varient_id);
                
                            $proData = Mst_store_product::find($varProdu->product_id);
                            
                    if ($proData->service_type != 2) {
                        
                        if (isset($varProdu)) {
                            
                            if ($request->quantity <= $varProdu->stock_count) {  //quantity shud be less dan current stock
                                //s1 p1
                            $totcountInCart = Trn_Cart::where('customer_id', $customer_id)->where('remove_status','=',0)->count();
                            if ($totcountInCart == 0)
                            {
                                        $proVarData = Mst_store_product_varient::find($request->product_varient_id);
                                        $cartItem = new Trn_Cart;
                                        $cartItem->store_id = $proVarData->store_id;
                                        $cartItem->customer_id = $request->customer_id;
                                        $cartItem->product_varient_id = $request->product_varient_id;
                                        $cartItem->product_id = $proVarData->product_id;
                                        $cartItem->quantity = $request->quantity;
                                        $cartItem->remove_status = 0;
                                        $cartItem->save();
        
                                        $data['status'] = 1;
                                        $data['message'] = "Product added to cart";
                                        return response($data);
                            }else{
                                if (Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status', 0)->where('product_varient_id', $request->product_varient_id)->first()) {

                                    $cartItem = Trn_Cart::where('customer_id', $request->customer_id)
                                        ->where('remove_status', 0)
                                        ->where('product_varient_id', $request->product_varient_id);
                                    $cartItem->update(['quantity' => $request->quantity]);
        
                                    $data['status'] = 1;
                                    $data['message'] = "Product added to cart";
                                    return response($data);
                                } else {

                                    if (Trn_Cart::where('customer_id', $customer_id)->where('store_id', '=', $request->store_id)->where('remove_status', 0)->get()->exists()) {  // to change 

                                        $proVarData = Mst_store_product_varient::find($request->product_varient_id);
                                        $cartItem = new Trn_Cart;
                                        $cartItem->store_id = $proVarData->store_id;
                                        $cartItem->customer_id = $request->customer_id;
                                        $cartItem->product_varient_id = $request->product_varient_id;
                                        $cartItem->product_id = $proVarData->product_id;
                                        $cartItem->quantity = $request->quantity;
                                        $cartItem->remove_status = 0;
                                        $cartItem->save();
        
                                        $data['status'] = 1;
                                        $data['message'] = "Product added to cart";
                                        return response($data);
                                    }else{
                                        $data['status'] = 2;
                                        $data['message'] = "Product of another store in cart. do you want to remove?"; 
                                    }
                                }

                            }

                                
                            } else {
                                $data['message'] = 'Stock unavailable';
                                $data['status'] = 2;
                                return response($data);
                            }
                    
                    } else {
                        $data['message'] = 'Product not found';
                        $data['status'] = 2;
                        return response($data);
                    }
                        
                    }else{
                        $data['status'] = 2;
                        $data['message'] = "Cannot add service product to cart";
                    }

                    
                    //old code

                        // if (isset($request->attributes)) {
                        //     foreach ($request->attributes as $row) {
                        //         $attr_group_id =  $row->attr_group_id;
                        //         $attr_value_id =  $row->attr_value_id;

                        //         $varAttrCount = Trn_ProductVariantAttribute::where('product_varient_id', $request->product_varient_id)
                        //             ->where('attr_group_id', $attr_group_id)
                        //             ->where('attr_value_id', $attr_value_id)->count();
                        //         if ($varAttrCount <= 0) {
                        //             $data['status'] = 0;
                        //             $data['message'] = "Product unavilable";
                        //             return $data;
                        //         }
                        //     }
                        // }



                        // if (Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status', 0)->where('product_varient_id', $request->product_varient_id)->first()) {
                        //     $cartItem = Trn_Cart::where('customer_id', $request->customer_id)
                        //         ->where('remove_status', 0)
                        //         ->where('product_varient_id', $request->product_varient_id);
                        //     // $cartItem->quantity = $request->quantity;
                        //     $cartItem->update(['quantity' => $request->quantity]);

                        //     $data['status'] = 1;
                        //     $data['message'] = "Product added to cart";
                        //     return response($data);
                        // } else {

                        //     $proVarData = Mst_store_product_varient::find($request->product_varient_id);

                        //     $cartItem = new Trn_Cart;
                        //     $cartItem->store_id = $proVarData->store_id;
                        //     $cartItem->customer_id = $request->customer_id;
                        //     $cartItem->product_varient_id = $request->product_varient_id;
                        //     $cartItem->product_id = $proVarData->product_id;
                        //     $cartItem->quantity = $request->quantity;
                        //     $cartItem->remove_status = 0;
                        //     $cartItem->save();

                        //     $data['status'] = 1;
                        //     $data['message'] = "Product added to cart";
                        //     return response($data);
                        // }
                    } else {
                         $data['status'] = 0;
                        $data['message'] = "failed";
                        $data['errors'] = $validator->errors();
                        return response($data);
                    }
                } else {
                    $data['status'] = 3;
                    $data['message'] = "Product not found";
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

    public function cartItems(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $customer_id = $request->customer_id;

                // $cartData = Trn_Cart::select('product_varient_id')
                // ->where('customer_id','=',$customer_id)
                // ->orderBy('cart_id','DESC')
                // ->get()->unique('product_varient_id')->pluck('product_varient_id')->toArray();

                if ($cartDatas = Trn_Cart::where('customer_id', $customer_id)->where('remove_status', 0)->get()) {
                    foreach ($cartDatas as $cartData) {
                        $cartData->productData =  Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                            ->select(
                                'mst_store_products.product_id',
                                'mst_store_products.product_name',
                                'mst_store_products.product_type',
                                'mst_store_products.service_type',
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
                                'mst_store_product_varients.store_id',
                                
                            )
                            ->where('mst_store_product_varients.product_varient_id', $cartData->product_varient_id)
                            ->where('mst_store_products.product_status', 1)->first();
                        @$cartData->productData->product_base_image = '/assets/uploads/products/base_product/base_image/' . @$cartData->productData->product_base_image;
                        @$cartData->productData->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . @$cartData->productData->product_varient_base_image;
                        $storeData = Mst_store::find(@$cartData->productData->store_id);
                        @$cartData->productData->store_name = @$storeData->store_name;

                        //attributes
                        $attributesData = Trn_ProductVariantAttribute::select('attr_group_id', 'attr_value_id')->where('product_varient_id', $cartData->product_varient_id)->get();
                        foreach ($attributesData as $j) {
                            $datas = Mst_attribute_group::where('attr_group_id', $j->attr_group_id)->first();
                            if (isset($datas->group_name))
                                $j->attr_group = @$datas->group_name;
                            else
                                $j->attr_group = '';

                            $datasvalue = Mst_attribute_value::where('attr_value_id', $j->attr_value_id)->first();
                            if (isset($datasvalue->group_value))
                                $j->attr_value = @$datasvalue->group_value;
                            else
                                $j->attr_value = '';
                        }
                        $cartData->attributesData = $attributesData;



                    }
                    // $product->quantity = Trn_Cart::where('product_varient_id',@$cartData->product_varient_id)->where('customer_id',$request->customer_id)->sum('quantity');

                    $data['cartItems'] = $cartDatas;
                    $data['status'] = 1;
                    $data['message'] = "success";
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                    return response($data);
                }
            } else {
                $data['status'] = 2;
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


    public function removeCartItems(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {

                // echo "here";die;  

                $cartData = Trn_Cart::where('product_varient_id', $request->product_varient_id)->where('customer_id', $request->customer_id)->first();
                if (isset($cartData)) {
                    if ($request->quantity == 0) {
                        Trn_Cart::where('product_varient_id', $request->product_varient_id)
                            ->where('customer_id', $request->customer_id)
                            ->update(['remove_status' =>  1]);
                    } else {
                        $cart = Trn_Cart::where('product_varient_id', $request->product_varient_id)->where('customer_id', $request->customer_id);
                        $cart->update(['quantity' =>  $request->quantity]);
                    }
                }

                $data['status'] = 1;
                $data['message'] = "Item removed";
            } else {
                $data['status'] = 3;
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


    public function updateQty(Request $request)
    {
        $data = array();
        try {
            if (isset($request->cart_id) && Trn_Cart::find($request->cart_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'quantity' => 'required|numeric',
                    ],
                    [
                        'quantity.required' => "Quantity required",
                    ]
                );
                if (!$validator->fails()) {
                    if (Trn_Cart::where('cart_id', @$request->cart_id)->where('remove_status', 0)->update(['quantity' => $request->quantity])) {
                        $data['status'] = 1;
                        $data['message'] = "Quantity updated";
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "Failed";
                    }
                } else {
                    $data['status'] = 2;
                    $data['message'] = "Quantity invalid";
                }
            } else {
                $data['status'] = 3;
                $data['message'] = "Item not found ";
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


    public function editAddress(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'address' => 'required',
                    ],
                    [
                        'address.required' => "address required",
                    ]
                );
                if (!$validator->fails()) {
                    if (Trn_store_customer::where('customer_id', @$request->customer_id)->update(['customer_address' => $request->address])) {
                        $data['status'] = 1;
                        $data['message'] = "Address updated";
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "Failed";
                    }
                } else {
                    $data['status'] = 2;
                    $data['message'] = "Address required";
                }
            } else {
                $data['status'] = 3;
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

    public function addAddress(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'address' => 'required',
                    ],
                    [
                        'address.required' => "address required",
                    ]
                );
                if (!$validator->fails()) {
                    if (Trn_store_customer::where('customer_id', @$request->customer_id)->update(['address_2' => $request->address])) {
                        $data['status'] = 1;
                        $data['message'] = "Address updated";
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "Failed";
                    }
                } else {
                    $data['status'] = 2;
                    $data['message'] = "Address required";
                }
            } else {
                $data['status'] = 3;
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


    public function upateAmount(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'total_amount' => 'required',
                ],
                [
                    'total_amount.required' => "Total amount required",
                ]
            );
            if (!$validator->fails()) {
                if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                    if (isset($request->coupon_code)) {


                        $coupon = Mst_Coupon::where('coupon_code', $request->coupon_code)->where('coupon_status', 0);
                        if (isset($request->store_id)) {
                            $coupon = $coupon->where('store_id', $request->store_id);
                        }
                        $coupon = $coupon->first();

                        if ($coupon) {
                            $current_time = Carbon::now()->toDateTimeString();

                            if ($coupon->valid_from >= $current_time) {
                                $data['status'] = 0;
                                $data['message'] = "Coupon not activated";
                            } else if ($coupon->valid_to <= $current_time) {
                                $data['status'] = 0;
                                $data['message'] = "Coupon expired";
                            } else if (($coupon->valid_from <= $current_time) && ($coupon->valid_to >= $current_time)) {
                                if ($request->total_amount >= $coupon->min_purchase_amt) {
                                    if ((Trn_store_order::where('customer_id', $request->customer_id)->where('coupon_code', $request->coupon_code)->whereIn('status_id', [6, 9, 4, 7, 8, 1])->count()) <= 0) {

                                        $ReducedAmount = 0;
                                        if ($coupon->discount_type == 1) {
                                            //fixedAmt
                                            $amtToBeReduced = $coupon->discount;
                                            $ReducedAmount = $request->total_amount - $coupon->discount;
                                        } else {
                                            //percentage
                                            $amtToBeReduced = ($coupon->discount * 100) / $request->total_amount;
                                            $ReducedAmount = $request->total_amount - $amtToBeReduced;
                                        }

                                        $data['status'] = 1;
                                        $data['discount_amount'] = number_format((float)$amtToBeReduced, 2, '.', '');
                                        $data['total_amount'] = number_format((float)$ReducedAmount, 2, '.', '');
                                        $data['message'] = "Coupon amount reduced";
                                    } else {

                                        if ($coupon->coupon_type == 2) {
                                            $ReducedAmount = 0;
                                            if ($coupon->discount_type == 1) {
                                                //fixedAmt
                                                $amtToBeReduced = $coupon->discount;

                                                $ReducedAmount = $request->total_amount - $coupon->discount;
                                            } else {
                                                //percentage
                                                $amtToBeReduced = ($coupon->discount * 100) / $request->total_amount;
                                                $ReducedAmount = $request->total_amount - $amtToBeReduced;
                                            }

                                            $data['status'] = 1;
                                            $data['discount_amount'] = number_format((float)$amtToBeReduced, 2, '.', '');
                                            $data['total_amount'] = number_format((float)$ReducedAmount, 2, '.', '');
                                            $data['message'] = "Coupon amount reduced";
                                        } else {
                                            $data['status'] = 0;
                                            $data['message'] = "Coupon already used";
                                        }
                                    }
                                } else {
                                    $data['status'] = 0;
                                    $data['message'] = "Minimum purchase amount should be " . $coupon->min_purchase_amt;
                                }
                            } else {
                                $data['status'] = 0;
                                $data['message'] = "Coupon not found";
                            }
                        } else {
                            $data['status'] = 0;
                            $data['message'] = "Coupon not applicable";
                        }
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "Coupon code required";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Customer not found";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Total Amount required";
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

    public function validateCoupon(Request $request)
    {
        $data = array();
        try {

            if (isset($request->coupon_code)) {

                $coupon = Mst_Coupon::where('coupon_code', $request->coupon_code)->where('coupon_status', 0);
                if (isset($request->store_id)) {
                    $coupon = $coupon->where('store_id', $request->store_id);
                }
                $coupon = $coupon->first();

                if ($coupon) {
                    $current_time = Carbon::now()->toDateTimeString();

                    if ($coupon->valid_from >= $current_time) {
                        $data['status'] = 0;
                        $data['message'] = "Coupon not activated";
                    } else if ($coupon->valid_to <= $current_time) {
                        $data['status'] = 0;
                        $data['message'] = "Coupon expired";
                    } else if (($coupon->valid_from <= $current_time) && ($coupon->valid_to >= $current_time)) {
                        if ($request->total_amount >= $coupon->min_purchase_amt) {
                            $data['status'] = 1;
                            $data['message'] = "Coupon active";
                        } else {
                            $data['status'] = 0;
                            $data['message'] = "Minimum purchase amount should be " . $coupon->min_purchase_amt;
                        }
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "Coupon not found";
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Coupon not found";
                }
            } else {
                $data['status'] = 0;
                $data['message'] = "Coupon code required";
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
}
