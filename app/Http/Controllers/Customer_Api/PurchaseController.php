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
use App\Models\admin\Trn_RecentlyVisitedProducts;
use App\Trn_wallet_log;
use DateTime;

class PurchaseController extends Controller
{



    public function reduceRewardPoint(Request $request)
    {
        $data = array();
        $store_id=$request->store_id;
        $orderAmount = $request->order_amount;
        $customerRewardPoint=0;
        $customerRewardStorePoint=0;
        $remainingOrderAmount=0;
        $data['totalReducableAmount'] =0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] = 0.00;
        $data['balancePoint'] = 0.00;
        $data['totalReducableStoreAmount'] =0.00;
        $data['reducedStoreOrderAmount'] = 0.00;
        $data['reducedAmountByStoreWalletPoints'] =0.00;
        $data['usedStorePoint'] = 0.00;
        $data['balanceStorePoint'] = 0.00;
        
        

       
            if (isset($request->order_amount) && isset($store_id) ) {
                if(number_format($orderAmount,2)<1.00)
                {
                    $data['status'] = 0;
                    $data['message'] = "Unable to redeem wallet points. Order amount cannot be less than Rs. 1";
                    return response($data);

                }
                /*$con=Trn_configure_points::where('store_id',$store_id)->first();
                if($con)
                {
                    if(number_format($orderAmount,2)<number_format($con->order_amount,2))
                    {
                        $data['status'] = 0;
                        $data['message'] = "Unable to redeem wallet points. Order amount cannot be less than Rs ".$con->order_amount;
                        return response($data);
    
                    }

                }*/
                if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                    $customer_id = $request->customer_id;
                    if($request->admin_points==1)
                    {
                        $totalCustomerRewardsCount = Trn_customer_reward::where('customer_id', $request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');
                        $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                        $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
    
                        $customerRewardPoint = ($totalCustomerRewardsCount - $totalusedPoints) - $redeemedPoints;

                    }
                    if($request->store_points==1)
                    {
                        $totalCustomerStoreRewardsCount =Trn_wallet_log::where('customer_id', $request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                        //$totalusedStorePoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used_store');
                        $totalusedStorePoints=Trn_wallet_log::where('customer_id', $request->customer_id)->where('store_id',$store_id)->sum('points_debited');
                        if($totalCustomerStoreRewardsCount-$totalusedStorePoints<0)
                        {
                            $data['status'] = 0;
                            $data['message'] = "Reward points can't be redeemed for store";
                            return response($data);

                        }
                        $redeemedStorePoints = 0;//Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                        $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
                        if($wallet_log_first->first())
                        {
                            $wallet_log_first->first()->delete();
                        }

    
                        //$customerRewardStorePoint = ($totalCustomerStoreRewardsCount - $totalusedStorePoints) - $redeemedStorePoints;
                        
                        $customerRewardStorePoint = $totalCustomerStoreRewardsCount - $totalusedStorePoints;
                    }
                 /* $data['adminPointTotal']=$totalCustomerRewardsCount;
                  $data['adminUsedPoint']=$totalusedPoints;
                  $data['adminPoint']=$customerRewardPoint;
                                   
                  $data['storePointTotal']=$totalCustomerStoreRewardsCount;
                  $data['storeUsedPoint']=$totalusedStorePoints;
                  $data['storePoint']=$customerRewardStorePoint;*/
                 // return response($data);

                    //echo $customerRewardPoint;die;

                    if (isset($customerRewardPoint) || isset($customerRewardStorePoint) ) {
                if(isset($customerRewardPoint))
                {
                    if($customerRewardPoint > 0)
                    {
                        if($request->store_points==0)
                        {
                            $totalCustomerStoreRewardsCount = 0;
                            $totalusedStorePoints = 0;
                           
                            $redeemedStorePoints = 0;
        
                            //$customerRewardStorePoint = 0;
                            $data['totalReducableStoreAmount'] =0.00;
                            $data['reducedStoreOrderAmount'] = 0.00;
                            $data['reducedAmountByStoreWalletPoints'] =0.00;
                            $data['usedStorePoint'] = 0.00;
                            $data['balanceStorePoint'] = 0.00;

                        }
                        $ConfigPoints = Trn_configure_points::first();
                        $pointToRupeeRatio =   $ConfigPoints->rupee / $ConfigPoints->rupee_points; // points to rupee ratio

                        $avilableRewardAmount = $pointToRupeeRatio * $customerRewardPoint;
                        $maxRedeemAmountPerOrder = $ConfigPoints->max_redeem_amount;
                        $totalReducableAmount = ($avilableRewardAmount * $ConfigPoints->redeem_percentage) / 100; // 10% of order amount

                        if ($totalReducableAmount > $maxRedeemAmountPerOrder) {
                        
                            $orderAmount = $request->order_amount;
                            $reducedOrderAmount = $orderAmount - $maxRedeemAmountPerOrder;
                            $customerUsedRewardPoint = $maxRedeemAmountPerOrder / $pointToRupeeRatio;
                            $remainingOrderAmount=$reducedOrderAmount;
                            if(number_format($customerUsedRewardPoint,2)==0.00)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for admin";
                                return response($data);

                            }
                            if ($reducedOrderAmount <= 0) {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for admin";
                                return response($data);
                            }

                            $data['orderAmount'] = number_format((float)$orderAmount, 2, '.', '');
                            $data['totalReducableAmount'] = number_format((float)$maxRedeemAmountPerOrder, 2, '.', '');
                            $data['reducedOrderAmount'] = number_format((float)$reducedOrderAmount, 2, '.', '');
                            $data['reducedAmountByWalletPoints'] = number_format((float)$maxRedeemAmountPerOrder, 2, '.', '');
                            $data['usedPoint'] = number_format((float)$customerUsedRewardPoint, 2, '.', '');
                            $data['balancePoint'] = number_format((float)$customerRewardPoint - $customerUsedRewardPoint, 2, '.', '');
                           
                        } else {

                            $orderAmount = $request->order_amount;
                            $reducedOrderAmount = $orderAmount - $totalReducableAmount;
                            $customerUsedRewardPoint = $totalReducableAmount / $pointToRupeeRatio;
                            if(number_format($customerUsedRewardPoint,2)==0.00)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for admin";
                                return response($data);

                            }
                            if ($reducedOrderAmount <= 0) {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for admin";
                                return response($data);
                            }
                            if($request->admin_points!=0)
                            {
                                $data['orderAmount'] = number_format((float)$orderAmount, 2, '.', '');
                                $data['totalReducableAmount'] = number_format((float)$totalReducableAmount, 2, '.', '');
                                $data['reducedOrderAmount'] = number_format((float)$reducedOrderAmount, 2, '.', '');
                                $data['reducedAmountByWalletPoints'] = number_format((float)$totalReducableAmount, 2, '.', '');
                                $data['usedPoint'] = number_format((float)$customerUsedRewardPoint, 2, '.', '');
                                $data['balancePoint'] = number_format((float)$customerRewardPoint - $customerUsedRewardPoint, 2, '.', '');

                            }
                            else
                            {
                                $data['orderAmount'] =0.00;
                                $data['totalReducableAmount'] = 0.00;
                                $data['reducedOrderAmount'] = 0.00;
                                $data['reducedAmountByWalletPoints'] =0.00;
                                $data['usedPoint'] =0.00;
                                $data['balancePoint'] = 0.00;

                            }
                           
                           // $orderAmount=$reducedOrderAmount;
                        }
                    }
                }
                if(isset($customerRewardStorePoint))
                {
                    if($customerRewardStorePoint > 0)
                    {
                        if($request->store_points==1)
                        {
                            if($request->admin_points==0)
                            {
                                $totalCustomerRewardsCount =0;
                                $totalusedPoints = 0;
                                $redeemedPoints = 0;
            
                                $customerRewardPoint = 0;
                                $data['totalReducableAmount'] =0.00;
                                $data['reducedOrderAmount'] = 0.00;
                                $data['reducedAmountByWalletPoints'] =0.00;
                                $data['usedPoint'] = 0.00;
                                $data['balancePoint'] = 0.00;
    
                            }

                        }
                       
                        $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                        $storePointToRupeeRatio =   $storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio

                        $avilableStoreRewardAmount = $storePointToRupeeRatio * $customerRewardStorePoint;
                        $storeMaxRedeemAmountPerOrder = $storeConfigPoints->max_redeem_amount;
                        $totalReducableStoreAmount = ($avilableStoreRewardAmount * $storeConfigPoints->redeem_percentage) / 100; // 10% of order amount

                        if ($totalReducableStoreAmount > $storeMaxRedeemAmountPerOrder) {

                            //$orderAmount = $request->order_amount;
                            $reducedOrderStoreAmount = $orderAmount - $storeMaxRedeemAmountPerOrder;
                            $remainingOrderAmount=$remainingOrderAmount-$storeMaxRedeemAmountPerOrder;
                            if($remainingOrderAmount<=0)
                            {
                                $remainingOrderAmount=$reducedOrderStoreAmount;
                            }
                            $customerUsedRewardStorePoint = $storeMaxRedeemAmountPerOrder / $storePointToRupeeRatio;
                            if(number_format($customerUsedRewardStorePoint,2)==0.00)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for store";
                                return response($data);

                            }
                            //return $customerUsedRewardStorePoint;
                            if ($remainingOrderAmount <= 0) {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for store";
                                return response($data);
                            }
                            $total_credit_points=Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                            $total_debit_points=Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_debited');
                           
                            $wallet_log=new Trn_wallet_log();
                            $wallet_log->store_id=$store_id;
                            $wallet_log->customer_id=$request->customer_id;
                            $wallet_log->type='debit';
                            $wallet_log->points_debited= number_format((float)$customerUsedRewardStorePoint, 2, '.', '');;
                            $wallet_log->points_credited=null;
                            $wallet_log->save();
                            $data['wallet_id']=$wallet_log->wallet_log_id;
                            $total_credit_points=Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                            $total_debit_points=Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_debited');
                            if($total_debit_points>$total_credit_points)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for store";
                                return response($data);

                            }
                            $data['orderAmount'] = number_format((float)$orderAmount, 2, '.', '');
                            $data['totalReducableStoreAmount'] = number_format((float)$storeMaxRedeemAmountPerOrder, 2, '.', '');
                            $data['reducedStoreOrderAmount'] = number_format((float)$reducedOrderStoreAmount, 2, '.', '');
                            $data['reducedAmountByStoreWalletPoints'] = number_format((float)$storeMaxRedeemAmountPerOrder, 2, '.', '');
                            $data['usedStorePoint'] = number_format((float)$customerUsedRewardStorePoint, 2, '.', '');
                            $data['balanceStorePoint'] =  $total_credit_points - $total_debit_points;

                     

                        } else {
                            if($request->admin_points==0)
                            {
                                //$$reducedOrderAmount=0;
    
                            }

                            $orderAmount = $orderAmount;
                            $reducedOrderStoreAmount = $orderAmount - $totalReducableStoreAmount;
                            $customerUsedRewardStorePoint = $totalReducableStoreAmount / $storePointToRupeeRatio;
                            if(number_format($customerUsedRewardStorePoint,2)==0.00)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for store";
                                return response($data);
                            }
                            //return $customerUsedRewardStorePoint;
                            if ($reducedOrderStoreAmount <= 0) {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed";
                                return response($data);
                            }
                            $remainingOrderAmount=$remainingOrderAmount-$storeMaxRedeemAmountPerOrder;
                            $data['orderAmount'] = number_format((float)$orderAmount, 2, '.', '');
                            $total_credit_points=Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                            $total_debit_points=Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_debited');
                           
                            $wallet_log=new Trn_wallet_log();
                            $wallet_log->store_id=$store_id;
                            $wallet_log->customer_id=$request->customer_id;
                            $wallet_log->type='debit';
                            $wallet_log->points_debited=$customerUsedRewardStorePoint;
                            $wallet_log->points_credited=null;
                            $wallet_log->save();
                            $data['wallet_id']=$wallet_log->wallet_log_id;
                            $total_credit_points=Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                            $total_debit_points=Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_debited');
                           
                            if($total_debit_points>$total_credit_points)
                            {
                                $data['status'] = 0;
                                $data['message'] = "Reward points can't be redeemed for store";
                                return response($data);

                            }
                            if($request->store_points!=0)
                            {
                                $data['totalReducableStoreAmount'] = number_format((float)$storeMaxRedeemAmountPerOrder, 2, '.', '');
                                $data['reducedStoreOrderAmount'] = number_format((float)$reducedOrderStoreAmount, 2, '.', '');
                                $data['reducedAmountByStoreWalletPoints'] = number_format((float)$storeMaxRedeemAmountPerOrder, 2, '.', '');
                                $data['usedStorePoint'] = number_format((float)$customerUsedRewardStorePoint, 2, '.', '');
                                $data['balanceStorePoint'] = $total_credit_points - $total_debit_points;
                            }
                            else{
                                $data['totalReducableStoreAmount'] =0.00;
                                $data['reducedStoreOrderAmount'] = 0.00;
                                $data['reducedAmountByStoreWalletPoints'] = 0.00;
                                $data['usedStorePoint'] = 0.00;
                                $data['balanceStorePoint'] = 0.00;

                            }
                            

                    }
                }
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
            $data['remainingOrderAmount'] = number_format((float)$remainingOrderAmount, 2, '.', '');

        if($this->checkReducedAmount($orderAmount,$data['reducedAmountByWalletPoints'],$data['reducedAmountByStoreWalletPoints'])==0)
        {
            $data['status'] = 0;
            $data['message'] = "Wallet reduced amount cannot be greater than order amount";
            return response($data);

        }
        else
        {
            return response($data);

        }
            
      
    }
    public function reduceRewardPointNew(Request $request)
    {
        $data = array();
        $orderTotalArray=array();
        $store_id=$request->store_id;
        $orderAmount = $request->order_amount;
        $data['totalReducableAmount'] =0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] = 0.00;
        $data['balancePoint'] = 0.00;
        $data['totalReducableStoreAmount'] =0.00;
        $data['reducedStoreOrderAmount'] = 0.00;
        $data['reducedAmountByStoreWalletPoints'] =0.00;
        $data['usedStorePoint'] = 0.00;
        $data['balanceStorePoint'] = 0.00;
        $data['orderAmount'] =number_format((float)$request->order_amount, 2, '.', '');
        $data['totalReducableAmount'] = 0.00;
        $data['totalReducableAdminAmount']=0.00;
        $data['totalReducableStoreAmount']=0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] =0.00;
        $data['balancePoint'] = 0.00;
        $data['remainingOrderAmount'] = 0.00;
        $orderTotalArray['orderAmountAdmin']=0.00;
        $orderTotalArray['orderAmountStore']=0.00;
        $max_reedem_set_admin=false;
        $max_reedem_set_store=false;
        
        

       
            if (isset($request->order_amount) ) {
                $orderAmount=$request->order_amount;
                if($request->admin_points==1)
                {
                    $adminConfigPoints = Trn_configure_points::first();
                    if($adminConfigPoints)
                    {
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $total_points=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//Admin wallet balance()
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                    $h=$total_points-$totalusedPoints-$redeemedPoints;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($j<=$e)
                    {
                        $k=$j;//Admin Redemption(Based on Max Redemption) (K)
                        if($d==100)
                        {
                            $k=$data['orderAmount']-1;
                            $max_reedem_set_admin=true;

                        }

                    }
                    else
                    {
                        $k=$e;//Admin Redemption(Based on Max Redemption) (K)
                        if($d==100)
                        {
                            $k=$data['orderAmount']-1;
                            $max_reedem_set_admin=true;

                        }
                    }
                    $balancePoints=$h-$k;
                    if($balancePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);
                    }
                    $l=$k*$f;//Admin Redemption Amount(RS)(L)
                    $i=$orderAmount;
                    if($i>=$l)
                    {
                        
                        $p=$i-$l;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = number_format((float)$p, 2, '.', '');;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }
                       

                    }
                    else
                    {
                        $p=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = 0.00;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }
                        

                    }
                    $data['reducedOrderAmount'] = number_format((float)$p, 2, '.', '');
                    $data['reducedAmountByWalletPoints'] =$l;
                    $data['usedPoint'] =number_format((float)$k, 2, '.', '');
                    $data['balancePoint'] = number_format((float)$balancePoints, 2, '.', '');
                    //$data['remainingOrderAmount'] = number_format((float)$p, 2, '.', '');

                    //$orderTotalArray['orderAmountAdmin']= $data['reducedOrderAmount'];
                    }
                    else
                    {
                        $data['status']=0;
                        $data['message'] = "No configure poins set for the admin";
                    }
         

                   }
                if($request->store_points==1)
                {
                    if(!$max_reedem_set_admin)
                    {
                    $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
                    if($wallet_log_first->first())
                    {
                        $wallet_log_first->first()->delete();
                    }
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    if($storeConfigPoints)
                    {
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $wallet_log_credited=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->where('store_id',$request->store_id)->sum('points_credited');
                    $wallet_log_redeemed=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->whereNotNull('order_id')->where('store_id',$request->store_id)->sum('points_debited');
                    $g=$wallet_log_credited-$wallet_log_redeemed;//Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                    $m=($g*$a)/100;

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$b)
                    {
                        $n=$m;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n = $data['orderAmount']-1;
                            $max_reedem_set_store=true;

                        }
                    }
                    else{
                        $n=$b;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n = $data['orderAmount']-1;
                            $max_reedem_set_store=true;

                        }
                    }
                    $balanceStorePoints=$g-$n;
                    if($balanceStorePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);
                    }
                    $o=$n*$c;//Admin Redemption Amount(RS)(L)
                    $i=$orderAmount;
                    if($i>=$o)
                    {
                        $q=$i-$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = number_format((float)$o, 2, '.', '');;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');

                        }

                    }
                    else
                    {
                        $q=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = 0.00;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');

                        }

                    }
                    $wallet_log=new Trn_wallet_log();
                    $wallet_log->store_id=$store_id;
                    $wallet_log->customer_id=$request->customer_id;
                    $wallet_log->type='debit';
                    $wallet_log->points_debited= number_format((float)$n, 2, '.', '');;
                    $wallet_log->points_credited=null;
                    $wallet_log->save();
                    $data['wallet_id']=$wallet_log->wallet_log_id;
                                               
                    //$data['totalReducableStoreAmount'] =number_format((float)$q, 2, '.', '');;
                    $data['reducedStoreOrderAmount'] = $q;
                    $data['reducedAmountByStoreWalletPoints'] =number_format((float)$n, 2, '.', '');
                    $data['usedStorePoint'] = number_format((float)$n, 2, '.', '');
                    $data['balanceStorePoint'] = number_format((float)$balanceStorePoints, 2, '.', '');
                }
                else
                {
                    $data['status'] = 0;
                    $data['message'] = "No configure points set for the store";
                    return response($data);

                }
            }

                }
                
                $data['totalReducableAmount']=$data['totalReducableStoreAmount']+$data['totalReducableAdminAmount'];
                $data['max_reedeem_admin']=$max_reedem_set_admin;
                 $data['max_reedeem_store']=$max_reedem_set_store;
                if($orderAmount>$data['totalReducableAmount'])
                {
                $r=$orderAmount-$data['totalReducableAmount'];
                }
                else
                {
                    $r=$orderAmount;
                }
                if($r<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Amount cannot be redeemmed now!";
                        return response($data);
                    }
            
                $data['remainingOrderAmount'] = number_format((float)$r, 2, '.', '');
                $data['status'] = 1;
                $data['message'] = "success";
                return response($data);
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Order amount required";
            }
            

        // if($this->checkReducedAmount($orderAmount,$data['reducedAmountByWalletPoints'],$data['reducedAmountByStoreWalletPoints'])==0)
        // {
        //     $data['status'] = 0;
        //     $data['message'] = "Wallet reduced amount cannot be greater than order amount";
        //     return response($data);

        // }
        // else
        // {
        //     return response($data);

        // }
            
      
    }
    public function reduceRewardPointLatest(Request $request)
    {
        $data = array();
        $orderTotalArray=array();
        $store_id=$request->store_id;
        $orderAmount = $request->order_amount;
        $data['totalReducableAmount'] =0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] = 0.00;
        $data['balancePoint'] = 0.00;
        $data['totalReducableStoreAmount'] =0.00;
        $data['reducedStoreOrderAmount'] = 0.00;
        $data['reducedAmountByStoreWalletPoints'] =0.00;
        $data['usedStorePoint'] = 0.00;
        $data['balanceStorePoint'] = 0.00;
        $data['orderAmount'] =number_format((float)$request->order_amount, 2, '.', '');
        $data['totalReducableAmount'] = 0.00;
        $data['totalReducableAdminAmount']=0.00;
        $data['totalReducableStoreAmount']=0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] =0.00;
        $data['balancePoint'] = 0.00;
        $data['remainingOrderAmount'] = 0.00;
        $orderTotalArray['orderAmountAdmin']=0.00;
        $orderTotalArray['orderAmountStore']=0.00;
        $max_reedem_set_admin=false;
        $max_reedem_set_store=false;
        
        

       
            if (isset($request->order_amount) ) {
                $orderAmount=$request->order_amount;
                if($request->admin_points==1)
                {
                    if(!$max_reedem_set_store)
                    {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $total_points=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//Admin wallet balance()
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                    $h=$total_points-$totalusedPoints-$redeemedPoints;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($j<=$data['orderAmount'])
                    {
                        $k=$j;//Admin Redemption(Based on Max Redemption) (K)
                       
                        if($d==100)
                        {
                            $k=$data['orderAmount']-1;
                            $max_reedem_set_admin=true;

                        }
                    }
                    else{
                        $k=$data['orderAmount']-1;//Admin Redemption(Based on Max Redemption) (K)
                        if($d==100)
                        {
                            $k=$data['orderAmount']-1;
                            $max_reedem_set_admin=true;

                        }
                    }
                    $balancePoints=$h-$k;
                    if($balancePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);
                    }
                    $l=$k*$f;//Admin Redemption Amount(RS)(L)
                    $i=$orderAmount;
                    if($i>=$l)
                    {
                        $p=$i-$l;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = number_format((float)$l, 2, '.', '');;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $p=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = 0.00;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }
                        

                    }
                    $data['reducedOrderAmount'] = number_format((float)$p, 2, '.', '');
                    $data['reducedAmountByWalletPoints'] =$l;
                    $data['usedPoint'] =number_format((float)$k, 2, '.', '');
                    $data['balancePoint'] = number_format((float)$balancePoints, 2, '.', '');
                    //$data['remainingOrderAmount'] = number_format((float)$p, 2, '.', '');

                    //$orderTotalArray['orderAmountAdmin']= $data['reducedOrderAmount'];

                }

                }
                if($request->store_points==1)
                {
                    if(!$max_reedem_set_admin)
                    {
                    $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
                    if($wallet_log_first->first())
                    {
                        $wallet_log_first->first()->delete();
                    }
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $wallet_log_credited=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->where('store_id',$request->store_id)->sum('points_credited');
                    $wallet_log_redeemed=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->whereNotNull('order_id')->where('store_id',$request->store_id)->sum('points_debited');
                    $g=$wallet_log_credited-$wallet_log_redeemed;//Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                    $m=($g*$a)/100;
                    //return $m;153.76<=100

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$data['orderAmount'])
                    {
                        $n=$m;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$data['orderAmount']-1;
                            $max_reedem_set_store=true;

                        }

                    }
                    else{
                        $n=$data['orderAmount']-1;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$data['orderAmount']-1;
                            $max_reedem_set_store=true;

                        }
                    }
                    $balanceStorePoints=$g-$n;
                    if($balanceStorePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);
                    }
                    $o=$n*$c;//Admin Redemption Amount(RS)(L)
                    $i=$orderAmount;
                    if($i>=$o)
                    {
                        $q=$i-$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = number_format((float)$o, 2, '.', '');;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $q=$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = 0.00;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    $wallet_log=new Trn_wallet_log();
                    $wallet_log->store_id=$store_id;
                    $wallet_log->customer_id=$request->customer_id;
                    $wallet_log->type='debit';
                    $wallet_log->points_debited= number_format((float)$n, 2, '.', '');;
                    $wallet_log->points_credited=null;
                    $wallet_log->save();
                    $data['wallet_id']=$wallet_log->wallet_log_id;
                                               
                    //$data['totalReducableStoreAmount'] =number_format((float)$q, 2, '.', '');;
                    $data['reducedStoreOrderAmount'] = $q;
                    $data['reducedAmountByStoreWalletPoints'] =number_format((float)$n, 2, '.', '');
                    $data['usedStorePoint'] = number_format((float)$n, 2, '.', '');
                    $data['balanceStorePoint'] = number_format((float)$balanceStorePoints, 2, '.', '');

                }
            }
                $data['totalReducableAmount']=$data['totalReducableStoreAmount']+$data['totalReducableAdminAmount'];
               
                //return $data['totalReducableAmount'];
                 $data['max_reedeem_admin']=$max_reedem_set_admin;
                 $data['max_reedeem_store']=$max_reedem_set_store;
                if($orderAmount<$data['totalReducableAmount'])
                {
                $r=$orderAmount-$data['totalReducableAmount'];
                }
                else
                {
                    $r=$orderAmount;
                }
                // if($r<=0)
                //     {
                //         $data['status'] = 0;
                //         $data['message'] = "Amount cannot be redeemmed now!";
                //         //return response($data);
                //     }
                $rem=$data['orderAmount']-$data['totalReducableAmount'];
                if($rem>0)
                {
                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');

                }
                else
                {
                    $rem=1;

                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');
                    
                }
                //return $rem;
            
                //$data['remainingOrderAmount'] = 
                $data['status']=1;
                $data['message']="success";
                return response($data);
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Order amount required";
            }
            

        // if($this->checkReducedAmount($orderAmount,$data['reducedAmountByWalletPoints'],$data['reducedAmountByStoreWalletPoints'])==0)
        // {
        //     $data['status'] = 0;
        //     $data['message'] = "Wallet reduced amount cannot be greater than order amount";
        //     return response($data);

        // }
        // else
        // {
        //     return response($data);

        // }
            
      
    }
    public function reduceRewardPointProcess(Request $request)
    {
        $data = array();
        $orderTotalArray=array();
        $store_id=$request->store_id;
        $orderAmount = $request->order_amount;
        $data['totalReducableAmount'] =0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] = 0.00;
        $data['balancePoint'] = 0.00;
        $data['totalReducableStoreAmount'] =0.00;
        $data['reducedStoreOrderAmount'] = 0.00;
        $data['reducedAmountByStoreWalletPoints'] =0.00;
        $data['usedStorePoint'] = 0.00;
        $data['balanceStorePoint'] = 0.00;
        $data['orderAmount'] =number_format((float)$request->order_amount, 2, '.', '');
        $data['totalReducableAmount'] = 0.00;
        $data['totalReducableAdminAmount']=0.00;
        $data['totalReducableStoreAmount']=0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] =0.00;
        $data['balancePoint'] = 0.00;
        $data['remainingOrderAmount'] = 0.00;
        $orderTotalArray['orderAmountAdmin']=0.00;
        $orderTotalArray['orderAmountStore']=0.00;
        $max_reedem_set_admin=false;
        $max_reedem_set_store=false;
        $relatableRedeemAmount=$data['orderAmount'];
        $redeem_preference=$request->redeem_preference;//1-Admin,2-Store,0-No
        $adminOrderAmount=$data['orderAmount'];
        $storeOrderAmount=$data['orderAmount'];
        
        

       
            if (isset($request->order_amount) ) {
                $orderAmount=$request->order_amount;
            if($request->admin_points==1&&$request->store_points==1) 
            { 
                if($redeem_preference==1)
                {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $total_points=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//Admin wallet balance()
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                    $h=$total_points-$totalusedPoints-$redeemedPoints;
                    //$h=$request->admin_wallet_balance;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');
                    if($j<=$relatableRedeemAmount)
                    {
                        $adminOrderAmount=$relatableRedeemAmount;
                        $storeOrderAmount=$relatableRedeemAmount-$j;
                    }
                    else
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);

                    }

                }
                if($redeem_preference==2)
                {
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $wallet_log_credited=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->where('store_id',$request->store_id)->sum('points_credited');
                    $wallet_log_redeemed=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->whereNotNull('order_id')->where('store_id',$request->store_id)->sum('points_debited');
                    $g=$wallet_log_credited-$wallet_log_redeemed;//Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                    ///$g=$request->store_wallet_balance;
                    $m=($g*$a)/100;
                    //return $m;153.76<=100

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$relatableRedeemAmount)
                    {
                        
                        $adminOrderAmount=$relatableRedeemAmount-$m;
                        $storeOrderAmount=$relatableRedeemAmount;
                        //return $adminOrderAmount;
                    }
                    else
                    {
                        
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);

                    }

                }
            }
                if($request->admin_points==1)
                {
                    if(!$max_reedem_set_store)
                    {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $total_points=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//Admin wallet balance()
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                    $h=$total_points-$totalusedPoints-$redeemedPoints;
                    //$h=$request->admin_wallet_balance;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($j<=$adminOrderAmount)
                    {
                        $k=$j;//Admin Redemption(Based on Max Redemption) (K)
                        //$relatableAdminRedeemAmount
                        //$relatableRedeemAmount=$relatableRedeemAmount-$j;
                       
                        if($d==100)
                        {
                            $k=$adminOrderAmount-1;
                            $max_reedem_set_admin=true;

                        }
                        if($e<=$j)
                        {
                            $k=$e;

                        }
                        //$relatableRedeemAmount=$relatableRedeemAmount-$k;
                        //return $k."6";
                       // $k=$r
                    }
                    else{
                        $k=$adminOrderAmount-1;//Admin Redemption(Based on Max Redemption) (K)
                        if($d==100)
                        {
                            $k=$adminOrderAmount-1;
                            $max_reedem_set_admin=true;

                        }
                        if($e<=$j)
                        {
                            $k=$e;

                        }
                        //$k=
                       // return $k."7";
                    }
                    $balancePoints=$h-$k;
                    if($balancePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);
                    }
                    $l=$k*$f;//Admin Redemption Amount(RS)(L)
                    $i=$adminOrderAmount;
                    if($i>=$l)
                    {
                        $p=$i-$l;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = number_format((float)$l, 2, '.', '');;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $p=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = 0.00;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }
                        

                    }
                    $data['reducedOrderAmount'] = number_format((float)$p, 2, '.', '');
                    $data['reducedAmountByWalletPoints'] =$l;
                    $data['usedPoint'] =number_format((float)$k, 2, '.', '');
                    $data['balancePoint'] = number_format((float)$balancePoints, 2, '.', '');
                    //$data['remainingOrderAmount'] = number_format((float)$p, 2, '.', '');

                    //$orderTotalArray['orderAmountAdmin']= $data['reducedOrderAmount'];

                }

                }
                if($request->store_points==1)
                {
                    if(!$max_reedem_set_admin)
                    {
                    $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
                    if($wallet_log_first->first())
                    {
                        $wallet_log_first->first()->delete();
                    }
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $wallet_log_credited=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->where('store_id',$request->store_id)->sum('points_credited');
                    $wallet_log_redeemed=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->whereNotNull('order_id')->where('store_id',$request->store_id)->sum('points_debited');
                    $g=$wallet_log_credited-$wallet_log_redeemed;//Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                    //$g=$request->store_wallet_balance;
                    $m=($g*$a)/100;
                    //return $m;153.76<=100

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$storeOrderAmount)
                    {
                        $n=$m;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$storeOrderAmount-1;
                            $max_reedem_set_store=true;

                        }
                        if($b<=$m)
                        {
                            $n=$b;

                        }

                    }
                    else{
                        $n=$storeOrderAmount-1;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$storeOrderAmount-1;
                            $max_reedem_set_store=true;

                        }
                        if($b<=$m)
                        {
                            $n=$b;

                        }
                    }
                    $balanceStorePoints=$g-$n;
                    if($balanceStorePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);
                    }
                    $o=$n*$c;//Admin Redemption Amount(RS)(L)
                    $i=$storeOrderAmount;
                    if($i>=$o)
                    {
                        $q=$i-$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = number_format((float)$o, 2, '.', '');;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $q=$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = 0.00;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    $wallet_log=new Trn_wallet_log();
                    $wallet_log->store_id=$store_id;
                    $wallet_log->customer_id=$request->customer_id;
                    $wallet_log->type='debit';
                    $wallet_log->points_debited= number_format((float)$n, 2, '.', '');;
                    $wallet_log->points_credited=null;
                    $wallet_log->save();
                    $data['wallet_id']=$wallet_log->wallet_log_id;
                                               
                    //$data['totalReducableStoreAmount'] =number_format((float)$q, 2, '.', '');;
                    $data['reducedStoreOrderAmount'] = number_format((float)$q, 2, '.', '');;;
                    $data['reducedAmountByStoreWalletPoints'] =number_format((float)$n, 2, '.', '');
                    $data['usedStorePoint'] = number_format((float)$n, 2, '.', '');
                    $data['balanceStorePoint'] = number_format((float)$balanceStorePoints, 2, '.', '');

                }
            }
                $data['totalReducableAmount']=$data['totalReducableStoreAmount']+$data['totalReducableAdminAmount'];
               
                //return $data['totalReducableAmount'];
                 $data['max_reedeem_admin']=$max_reedem_set_admin;
                 $data['max_reedeem_store']=$max_reedem_set_store;
                if($orderAmount<$data['totalReducableAmount'])
                {
                $r=$orderAmount-$data['totalReducableAmount'];
                }
                else
                {
                    $r=$orderAmount;
                }
                // if($r<=0)
                //     {
                //         $data['status'] = 0;
                //         $data['message'] = "Amount cannot be redeemmed now!";
                //         //return response($data);
                //     }
                $rem=$data['orderAmount']-$data['totalReducableAmount'];
                if($rem>0)
                {
                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');

                }
                else
                {
                    $rem=1;

                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');
                    
                }
                //return $rem;
            
                //$data['remainingOrderAmount'] = 
                $data['status']=1;
                $data['message']="success";
                return response($data);
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Order amount required";
            }
            

        // if($this->checkReducedAmount($orderAmount,$data['reducedAmountByWalletPoints'],$data['reducedAmountByStoreWalletPoints'])==0)
        // {
        //     $data['status'] = 0;
        //     $data['message'] = "Wallet reduced amount cannot be greater than order amount";
        //     return response($data);

        // }
        // else
        // {
        //     return response($data);

        // }
            
      
    }
    public function reduceRewardPointChk(Request $request)
    {
        $data = array();
        $orderTotalArray=array();
        $store_id=$request->store_id;
        $orderAmount = $request->order_amount;
        $data['totalReducableAmount'] =0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] = 0.00;
        $data['balancePoint'] = 0.00;
        $data['totalReducableStoreAmount'] =0.00;
        $data['reducedStoreOrderAmount'] = 0.00;
        $data['reducedAmountByStoreWalletPoints'] =0.00;
        $data['usedStorePoint'] = 0.00;
        $data['balanceStorePoint'] = 0.00;
        $data['orderAmount'] =number_format((float)$request->order_amount, 2, '.', '');
        $data['totalReducableAmount'] = 0.00;
        $data['totalReducableAdminAmount']=0.00;
        $data['totalReducableStoreAmount']=0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] =0.00;
        $data['balancePoint'] = 0.00;
        $data['remainingOrderAmount'] = 0.00;
        $orderTotalArray['orderAmountAdmin']=0.00;
        $orderTotalArray['orderAmountStore']=0.00;
        $max_reedem_set_admin=false;
        $max_reedem_set_store=false;
        $relatableRedeemAmount=$data['orderAmount'];
        $redeem_preference=$request->redeem_preference;//1-Admin,2-Store,0-No
        $adminOrderAmount=$data['orderAmount'];
        $storeOrderAmount=$data['orderAmount'];
        
        

       
            if (isset($request->order_amount) ) {
            $orderAmount=$request->order_amount;
            if($request->admin_points==1&&$request->store_points==1) 
            { 
                if($redeem_preference==1)
                {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $total_points=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//Admin wallet balance()
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                    //$h=$total_points-$totalusedPoints-$redeemedPoints;
                    $h=$request->admin_wallet_balance;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');
                    if($j<=$relatableRedeemAmount)
                    {
                        $adminOrderAmount=$relatableRedeemAmount;
                        $storeOrderAmount=$relatableRedeemAmount-$j;
                    }
                    else
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);

                    }

                }
                if($redeem_preference==2)
                {
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $wallet_log_credited=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->where('store_id',$request->store_id)->sum('points_credited');
                    $wallet_log_redeemed=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->whereNotNull('order_id')->where('store_id',$request->store_id)->sum('points_debited');
                    $g=$wallet_log_credited-$wallet_log_redeemed;//Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                    $g=$request->store_wallet_balance;
                    $m=($g*$a)/100;
                    //return $m;153.76<=100

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$relatableRedeemAmount)
                    {
                        
                        $adminOrderAmount=$relatableRedeemAmount-$m;
                        $storeOrderAmount=$relatableRedeemAmount;
                        //return $adminOrderAmount;
                    }
                    else
                    {
                        
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);

                    }

                }
            }
                if($request->admin_points==1)
                {
                    if(!$max_reedem_set_store)
                    {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $total_points=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//Admin wallet balance()
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                    //$h=$total_points-$totalusedPoints-$redeemedPoints;
                    $h=$request->admin_wallet_balance;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($j<=$adminOrderAmount)
                    {
                        $k=$j;//Admin Redemption(Based on Max Redemption) (K)
                        //$relatableAdminRedeemAmount
                        //$relatableRedeemAmount=$relatableRedeemAmount-$j;
                       
                        if($d==100)
                        {
                            $k=$adminOrderAmount-1;
                            $max_reedem_set_admin=true;

                        }
                        //$relatableRedeemAmount=$relatableRedeemAmount-$k;
                        //return $k."6";
                       // $k=$r
                    }
                    else{
                        $k=$adminOrderAmount-1;//Admin Redemption(Based on Max Redemption) (K)
                        if($d==100)
                        {
                            $k=$adminOrderAmount-1;
                            $max_reedem_set_admin=true;

                        }
                        //$k=
                       // return $k."7";
                    }
                    $balancePoints=$h-$k;
                    if($balancePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);
                    }
                    $l=$k*$f;//Admin Redemption Amount(RS)(L)
                    $i=$adminOrderAmount;
                    if($i>=$l)
                    {
                        $p=$i-$l;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = number_format((float)$l, 2, '.', '');;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $p=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = 0.00;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }
                        

                    }
                    $data['reducedOrderAmount'] = number_format((float)$p, 2, '.', '');
                    $data['reducedAmountByWalletPoints'] =$l;
                    $data['usedPoint'] =number_format((float)$k, 2, '.', '');
                    $data['balancePoint'] = number_format((float)$balancePoints, 2, '.', '');
                    //$data['remainingOrderAmount'] = number_format((float)$p, 2, '.', '');

                    //$orderTotalArray['orderAmountAdmin']= $data['reducedOrderAmount'];

                }

                }
                if($request->store_points==1)
                {
                    if(!$max_reedem_set_admin)
                    {
                    $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
                    if($wallet_log_first->first())
                    {
                        $wallet_log_first->first()->delete();
                    }
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $wallet_log_credited=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->where('store_id',$request->store_id)->sum('points_credited');
                    $wallet_log_redeemed=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->whereNotNull('order_id')->where('store_id',$request->store_id)->sum('points_debited');
                    //$g=$wallet_log_credited-$wallet_log_redeemed;//Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                    $g=$request->store_wallet_balance;
                    $m=($g*$a)/100;
                    //return $m;153.76<=100

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$storeOrderAmount)
                    {
                        $n=$m;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$storeOrderAmount-1;
                            $max_reedem_set_store=true;

                        }

                    }
                    else{
                        $n=$storeOrderAmount-1;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$storeOrderAmount-1;
                            $max_reedem_set_store=true;

                        }
                    }
                    $balanceStorePoints=$g-$n;
                    if($balanceStorePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);
                    }
                    $o=$n*$c;//Admin Redemption Amount(RS)(L)
                    $i=$storeOrderAmount;
                    if($i>=$o)
                    {
                        $q=$i-$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = number_format((float)$o, 2, '.', '');;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $q=$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = 0.00;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    $wallet_log=new Trn_wallet_log();
                    $wallet_log->store_id=$store_id;
                    $wallet_log->customer_id=$request->customer_id;
                    $wallet_log->type='debit';
                    $wallet_log->points_debited= number_format((float)$n, 2, '.', '');;
                    $wallet_log->points_credited=null;
                    $wallet_log->save();
                    $data['wallet_id']=$wallet_log->wallet_log_id;
                                               
                    //$data['totalReducableStoreAmount'] =number_format((float)$q, 2, '.', '');;
                    $data['reducedStoreOrderAmount'] = $q;
                    $data['reducedAmountByStoreWalletPoints'] =number_format((float)$n, 2, '.', '');
                    $data['usedStorePoint'] = number_format((float)$n, 2, '.', '');
                    $data['balanceStorePoint'] = number_format((float)$balanceStorePoints, 2, '.', '');

                }
            }
                $data['totalReducableAmount']=$data['totalReducableStoreAmount']+$data['totalReducableAdminAmount'];
               
                //return $data['totalReducableAmount'];
                 $data['max_reedeem_admin']=$max_reedem_set_admin;
                 $data['max_reedeem_store']=$max_reedem_set_store;
                if($orderAmount<$data['totalReducableAmount'])
                {
                $r=$orderAmount-$data['totalReducableAmount'];
                }
                else
                {
                    $r=$orderAmount;
                }
                // if($r<=0)
                //     {
                //         $data['status'] = 0;
                //         $data['message'] = "Amount cannot be redeemmed now!";
                //         //return response($data);
                //     }
                $rem=$data['orderAmount']-$data['totalReducableAmount'];
                if($rem>0)
                {
                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');

                }
                else
                {
                    $rem=1;

                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');
                    
                }
                //return $rem;
            
                //$data['remainingOrderAmount'] = 
                $data['status']=1;
                $data['message']="success";
                return response($data);
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Order amount required";
            }
            

        // if($this->checkReducedAmount($orderAmount,$data['reducedAmountByWalletPoints'],$data['reducedAmountByStoreWalletPoints'])==0)
        // {
        //     $data['status'] = 0;
        //     $data['message'] = "Wallet reduced amount cannot be greater than order amount";
        //     return response($data);

        // }
        // else
        // {
        //     return response($data);

        // }
            
      
    }
    public function reduceRewardPointCheck(Request $request)
    {
        $data = array();
        $orderTotalArray=array();
        $store_id=$request->store_id;
        $orderAmount = $request->order_amount;
        $data['totalReducableAmount'] =0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] = 0.00;
        $data['balancePoint'] = 0.00;
        $data['totalReducableStoreAmount'] =0.00;
        $data['reducedStoreOrderAmount'] = 0.00;
        $data['reducedAmountByStoreWalletPoints'] =0.00;
        $data['usedStorePoint'] = 0.00;
        $data['balanceStorePoint'] = 0.00;
        $data['orderAmount'] =number_format((float)$request->order_amount, 2, '.', '');
        $data['totalReducableAmount'] = 0.00;
        $data['totalReducableAdminAmount']=0.00;
        $data['totalReducableStoreAmount']=0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] =0.00;
        $data['balancePoint'] = 0.00;
        $data['remainingOrderAmount'] = 0.00;
        $orderTotalArray['orderAmountAdmin']=0.00;
        $orderTotalArray['orderAmountStore']=0.00;
        $max_reedem_set_admin=false;
        $max_reedem_set_store=false;
        $relatableRedeemAmount=$data['orderAmount'];
        $redeem_preference=$request->redeem_preference;//1-Admin,2-Store,0-No
        $adminOrderAmount=$data['orderAmount'];
        $storeOrderAmount=$data['orderAmount'];
        
        

       
            if (isset($request->order_amount) ) {
                $orderAmount=$request->order_amount;
            if($request->admin_points==1&&$request->store_points==1) 
            { 
                if($redeem_preference==1)
                {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $total_points=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//Admin wallet balance()
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                    //$h=$total_points-$totalusedPoints-$redeemedPoints;
                    $h=$request->admin_wallet_balance;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');
                    if($j<=$relatableRedeemAmount)
                    {
                        $adminOrderAmount=$relatableRedeemAmount;
                        $storeOrderAmount=$relatableRedeemAmount-$j;
                    }
                    else
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);

                    }

                }
                if($redeem_preference==2)
                {
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $wallet_log_credited=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->where('store_id',$request->store_id)->sum('points_credited');
                    $wallet_log_redeemed=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->whereNotNull('order_id')->where('store_id',$request->store_id)->sum('points_debited');
                    //$g=$wallet_log_credited-$wallet_log_redeemed;//Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                    $g=$request->store_wallet_balance;
                    $m=($g*$a)/100;
                    //return $m;153.76<=100

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$relatableRedeemAmount)
                    {
                        
                        $adminOrderAmount=$relatableRedeemAmount-$m;
                        $storeOrderAmount=$relatableRedeemAmount;
                        //return $adminOrderAmount;
                    }
                    else
                    {
                        
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);

                    }

                }
            }
                if($request->admin_points==1)
                {
                    if(!$max_reedem_set_store)
                    {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $total_points=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//Admin wallet balance()
                    $totalusedPoints = Trn_store_order::where('customer_id', $request->customer_id)->whereNotIn('status_id', [5])->sum('reward_points_used');
                    $redeemedPoints = Trn_points_redeemed::where('customer_id', $request->customer_id)->sum('points');
                    //$h=$total_points-$totalusedPoints-$redeemedPoints;
                    $h=$request->admin_wallet_balance;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($j<=$adminOrderAmount)
                    {
                        $k=$j;//Admin Redemption(Based on Max Redemption) (K)
                        //$relatableAdminRedeemAmount
                        //$relatableRedeemAmount=$relatableRedeemAmount-$j;
                       
                        if($d==100)
                        {
                            $k=$adminOrderAmount-1;
                            $max_reedem_set_admin=true;

                        }
                        //$relatableRedeemAmount=$relatableRedeemAmount-$k;
                        //return $k."6";
                       // $k=$r
                    }
                    else{
                        $k=$adminOrderAmount-1;//Admin Redemption(Based on Max Redemption) (K)
                        if($d==100)
                        {
                            $k=$adminOrderAmount-1;
                            $max_reedem_set_admin=true;

                        }
                        //$k=
                       // return $k."7";
                    }
                    $balancePoints=$h-$k;
                    if($balancePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);
                    }
                    $l=$k*$f;//Admin Redemption Amount(RS)(L)
                    $i=$adminOrderAmount;
                    if($i>=$l)
                    {
                        $p=$i-$l;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = number_format((float)$l, 2, '.', '');;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $p=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = 0.00;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }
                        

                    }
                    $data['reducedOrderAmount'] = number_format((float)$p, 2, '.', '');
                    $data['reducedAmountByWalletPoints'] =$l;
                    $data['usedPoint'] =number_format((float)$k, 2, '.', '');
                    $data['balancePoint'] = number_format((float)$balancePoints, 2, '.', '');
                    //$data['remainingOrderAmount'] = number_format((float)$p, 2, '.', '');

                    //$orderTotalArray['orderAmountAdmin']= $data['reducedOrderAmount'];

                }

                }
                if($request->store_points==1)
                {
                    if(!$max_reedem_set_admin)
                    {
                    $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
                    if($wallet_log_first->first())
                    {
                        $wallet_log_first->first()->delete();
                    }
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $wallet_log_credited=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->where('store_id',$request->store_id)->sum('points_credited');
                    $wallet_log_redeemed=Trn_wallet_log::where('customer_id',$request->customer_id)->whereNotNull('store_id')->whereNotNull('order_id')->where('store_id',$request->store_id)->sum('points_debited');
                    //$g=$wallet_log_credited-$wallet_log_redeemed;//Trn_wallet_log::where('customer_id',$request->customer_id)->where('store_id',$store_id)->sum('points_credited');
                    $g=$request->store_wallet_balance;
                    $m=($g*$a)/100;
                    //return $m;153.76<=100

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$storeOrderAmount)
                    {
                        $n=$m;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$storeOrderAmount-1;
                            $max_reedem_set_store=true;

                        }

                    }
                    else{
                        $n=$storeOrderAmount-1;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$storeOrderAmount-1;
                            $max_reedem_set_store=true;

                        }
                    }
                    $balanceStorePoints=$g-$n;
                    if($balanceStorePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);
                    }
                    $o=$n*$c;//Admin Redemption Amount(RS)(L)
                    $i=$storeOrderAmount;
                    if($i>=$o)
                    {
                        $q=$i-$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = number_format((float)$o, 2, '.', '');;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $q=$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = 0.00;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    $wallet_log=new Trn_wallet_log();
                    $wallet_log->store_id=$store_id;
                    $wallet_log->customer_id=$request->customer_id;
                    $wallet_log->type='debit';
                    $wallet_log->points_debited= number_format((float)$n, 2, '.', '');;
                    $wallet_log->points_credited=null;
                    $wallet_log->save();
                    $data['wallet_id']=$wallet_log->wallet_log_id;
                                               
                    //$data['totalReducableStoreAmount'] =number_format((float)$q, 2, '.', '');;
                    $data['reducedStoreOrderAmount'] = $q;
                    $data['reducedAmountByStoreWalletPoints'] =number_format((float)$n, 2, '.', '');
                    $data['usedStorePoint'] = number_format((float)$n, 2, '.', '');
                    $data['balanceStorePoint'] = number_format((float)$balanceStorePoints, 2, '.', '');

                }
            }
                $data['totalReducableAmount']=$data['totalReducableStoreAmount']+$data['totalReducableAdminAmount'];
               
                //return $data['totalReducableAmount'];
                 $data['max_reedeem_admin']=$max_reedem_set_admin;
                 $data['max_reedeem_store']=$max_reedem_set_store;
                if($orderAmount<$data['totalReducableAmount'])
                {
                $r=$orderAmount-$data['totalReducableAmount'];
                }
                else
                {
                    $r=$orderAmount;
                }
                // if($r<=0)
                //     {
                //         $data['status'] = 0;
                //         $data['message'] = "Amount cannot be redeemmed now!";
                //         //return response($data);
                //     }
                $rem=$data['orderAmount']-$data['totalReducableAmount'];
                if($rem>0)
                {
                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');

                }
                else
                {
                    $rem=1;

                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');
                    
                }
                //return $rem;
            
                //$data['remainingOrderAmount'] = 
                $data['status']=1;
                $data['message']="success";
                return response($data);
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Order amount required";
            }
            

        // if($this->checkReducedAmount($orderAmount,$data['reducedAmountByWalletPoints'],$data['reducedAmountByStoreWalletPoints'])==0)
        // {
        //     $data['status'] = 0;
        //     $data['message'] = "Wallet reduced amount cannot be greater than order amount";
        //     return response($data);

        // }
        // else
        // {
        //     return response($data);

        // }
            
      
    }
    public function reduceRewardPointTest(Request $request)
    {
        $data = array();
        $orderTotalArray=array();
        $store_id=$request->store_id;
        $orderAmount = $request->order_amount;
        $data['totalReducableAmount'] =0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] = 0.00;
        $data['balancePoint'] = 0.00;
        $data['totalReducableStoreAmount'] =0.00;
        $data['reducedStoreOrderAmount'] = 0.00;
        $data['reducedAmountByStoreWalletPoints'] =0.00;
        $data['usedStorePoint'] = 0.00;
        $data['balanceStorePoint'] = 0.00;
        $data['orderAmount'] =number_format((float)$request->order_amount, 2, '.', '');
        $data['totalReducableAmount'] = 0.00;
        $data['totalReducableAdminAmount']=0.00;
        $data['totalReducableStoreAmount']=0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] =0.00;
        $data['balancePoint'] = 0.00;
        $data['remainingOrderAmount'] = 0.00;
        $orderTotalArray['orderAmountAdmin']=0.00;
        $orderTotalArray['orderAmountStore']=0.00;
        $max_reedem_set_admin=false;
        $max_reedem_set_store=false;
        
        

       
            if (isset($request->order_amount) ) {
                $orderAmount=$request->order_amount;
                if($request->admin_points==1)
                {
                    if(!$max_reedem_set_store)
                    {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $h=$request->admin_wallet_balance;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($j<=$e)
                    {
                        $k=$j;//Admin Redemption(Based on Max Redemption) (K)
                       
                        if($d==100)
                        {
                            $k=$data['orderAmount']-1;
                            $max_reedem_set_admin=true;

                        }
                    }
                    else{
                        $k=$e;//Admin Redemption(Based on Max Redemption) (K)
                        if($d==100)
                        {
                            $k=$data['orderAmount']-1;
                            $max_reedem_set_admin=true;

                        }
                    }
                    $balancePoints=$h-$k;
                    if($balancePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);
                    }
                    $l=$k*$f;//Admin Redemption Amount(RS)(L)
                    $i=$orderAmount;
                    if($i>=$l)
                    {
                        $p=$i-$l;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = number_format((float)$l, 2, '.', '');;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $p=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = 0.00;
                        if($d==100)
                        {
                            $data['totalReducableAdminAmount'] = number_format((float)$k, 2, '.', '');;

                        }
                        

                    }
                    $data['reducedOrderAmount'] = number_format((float)$p, 2, '.', '');
                    $data['reducedAmountByWalletPoints'] =$l;
                    $data['usedPoint'] =number_format((float)$k, 2, '.', '');
                    $data['balancePoint'] = number_format((float)$balancePoints, 2, '.', '');
                    //$data['remainingOrderAmount'] = number_format((float)$p, 2, '.', '');

                    //$orderTotalArray['orderAmountAdmin']= $data['reducedOrderAmount'];

                }

                }
                if($request->store_points==1)
                {
                    if(!$max_reedem_set_admin)
                    {
                    $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
                    if($wallet_log_first->first())
                    {
                        $wallet_log_first->first()->delete();
                    }
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $g=$request->store_wallet_balance;
                    $m=($g*$a)/100;
                    //return $m;153.76<=100

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$b)
                    {
                        $n=$m;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$data['orderAmount']-1;
                            $max_reedem_set_store=true;

                        }

                    }
                    else{
                        $n=$b;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$data['orderAmount']-1;
                            $max_reedem_set_store=true;

                        }
                    }
                    $balanceStorePoints=$g-$n;
                    if($balanceStorePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);
                    }
                    $o=$n*$c;//Admin Redemption Amount(RS)(L)
                    $i=$orderAmount;
                    if($i>=$o)
                    {
                        $q=$i-$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = number_format((float)$o, 2, '.', '');;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $q=$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = 0.00;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    $wallet_log=new Trn_wallet_log();
                    $wallet_log->store_id=$store_id;
                    $wallet_log->customer_id=$request->customer_id;
                    $wallet_log->type='debit';
                    $wallet_log->points_debited= number_format((float)$n, 2, '.', '');;
                    $wallet_log->points_credited=null;
                    $wallet_log->save();
                    $data['wallet_id']=$wallet_log->wallet_log_id;
                                               
                    //$data['totalReducableStoreAmount'] =number_format((float)$q, 2, '.', '');;
                    $data['reducedStoreOrderAmount'] = $q;
                    $data['reducedAmountByStoreWalletPoints'] =number_format((float)$n, 2, '.', '');
                    $data['usedStorePoint'] = number_format((float)$n, 2, '.', '');
                    $data['balanceStorePoint'] = number_format((float)$balanceStorePoints, 2, '.', '');

                }
            }
                $data['totalReducableAmount']=$data['totalReducableStoreAmount']+$data['totalReducableAdminAmount'];
               
                //return $data['totalReducableAmount'];
                 $data['max_reedeem_admin']=$max_reedem_set_admin;
                 $data['max_reedeem_store']=$max_reedem_set_store;
                if($orderAmount<$data['totalReducableAmount'])
                {
                $r=$orderAmount-$data['totalReducableAmount'];
                }
                else
                {
                    $r=$orderAmount;
                }
                // if($r<=0)
                //     {
                //         $data['status'] = 0;
                //         $data['message'] = "Amount cannot be redeemmed now!";
                //         //return response($data);
                //     }
                $rem=$data['orderAmount']-$data['totalReducableAmount'];
                if($rem>0)
                {
                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');

                }
                else
                {
                    $rem=1;

                    $data['remainingOrderAmount']=number_format((float)$rem, 2, '.', '');
                    
                }
                //return $rem;
            
                //$data['remainingOrderAmount'] = 
                $data['status']=1;
                $data['message']="success";
                return response($data);
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Order amount required";
            }
            

        // if($this->checkReducedAmount($orderAmount,$data['reducedAmountByWalletPoints'],$data['reducedAmountByStoreWalletPoints'])==0)
        // {
        //     $data['status'] = 0;
        //     $data['message'] = "Wallet reduced amount cannot be greater than order amount";
        //     return response($data);

        // }
        // else
        // {
        //     return response($data);

        // }
            
      
    }
    public function reduceRewardPointTest2(Request $request)
    {
        $data = array();
        $orderTotalArray=array();
        $store_id=$request->store_id;
        $orderAmount = $request->order_amount;
        $data['totalReducableAmount'] =0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] = 0.00;
        $data['balancePoint'] = 0.00;
        $data['totalReducableStoreAmount'] =0.00;
        $data['reducedStoreOrderAmount'] = 0.00;
        $data['reducedAmountByStoreWalletPoints'] =0.00;
        $data['usedStorePoint'] = 0.00;
        $data['balanceStorePoint'] = 0.00;
        $data['orderAmount'] =number_format((float)$request->order_amount, 2, '.', '');
        $data['totalReducableAmount'] = 0.00;
        $data['totalReducableAdminAmount']=0.00;
        $data['totalReducableStoreAmount']=0.00;
        $data['reducedOrderAmount'] = 0.00;
        $data['reducedAmountByWalletPoints'] =0.00;
        $data['usedPoint'] =0.00;
        $data['balancePoint'] = 0.00;
        $data['remainingOrderAmount'] = 0.00;
        $orderTotalArray['orderAmountAdmin']=0.00;
        $orderTotalArray['orderAmountStore']=0.00;
        
        

       
            if (isset($request->order_amount) ) {
                $orderAmount=$request->order_amount;
                if($request->admin_points==1)
                {
                    $adminConfigPoints = Trn_configure_points::first();
                    $d=$adminConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(D)
                    $e=$adminConfigPoints->max_redeem_amount;//Max. Amount Redeemable (E)
                    $f=$adminConfigPoints->rupee / $adminConfigPoints->rupee_points; // points to rupee ratio(F)
                    $h=$request->admin_wallet_balance;
                    //$h=30;
                    $j=($h*$d)/100;

                    $j=number_format((float)$j, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($j<=$e)
                    {
                        $k=$j;//Admin Redemption(Based on Max Redemption) (K)
                       
                        if($d==100)
                        {
                            $k=$data['orderAmount']-1;

                        }
                    }
                    else{
                        $k=$e;//Admin Redemption(Based on Max Redemption) (K)
                        if($d==100)
                        {
                            $k=$data['orderAmount']-1;

                        }
                    }
                    $balancePoints=$h-$k;
                    if($balancePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for admin";
                        return response($data);
                    }
                    $l=$k*$f;//Admin Redemption Amount(RS)(L)
                    $i=$orderAmount;
                    if($i>=$l)
                    {
                        $p=$i-$l;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = number_format((float)$l, 2, '.', '');;
                        if($d==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$k, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $p=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableAdminAmount'] = 0.00;
                        if($d==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$k, 2, '.', '');;

                        }
                        

                    }
                    $data['reducedOrderAmount'] = number_format((float)$p, 2, '.', '');
                    $data['reducedAmountByWalletPoints'] =$l;
                    $data['usedPoint'] =number_format((float)$k, 2, '.', '');
                    $data['balancePoint'] = number_format((float)$balancePoints, 2, '.', '');
                    //$data['remainingOrderAmount'] = number_format((float)$p, 2, '.', '');

                    //$orderTotalArray['orderAmountAdmin']= $data['reducedOrderAmount'];



                }
                if($request->store_points==1)
                {
                    $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
                    if($wallet_log_first->first())
                    {
                        $wallet_log_first->first()->delete();
                    }
                    $storeConfigPoints=Trn_configure_points::where('store_id',$store_id)->first();
                    $a=$storeConfigPoints->redeem_percentage;//% of Wallet Amount Redeemable(A)
                    $b=$storeConfigPoints->max_redeem_amount;//Max. Amount Redeemable (B)
                    $c=$storeConfigPoints->rupee / $storeConfigPoints->rupee_points; // points to rupee ratio(C)
                    //$g=Trn_customer_reward::where('customer_id',$request->customer_id)->where('reward_point_status', 1)->whereNull('store_id')->where('discription','!=','store points')->sum('reward_points_earned');//store wallet balance(G)
                    //$g=50;
                    $g=$request->store_wallet_balance;
                    $m=($g*$a)/100;

                    $m=number_format((float)$m, 2, '.', '');//Admin Redemption Points (Actual) (J)
                    if($m<=$b)
                    {
                        $n=$m;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$data['orderAmount']-1;

                        }

                    }
                    else{
                        $n=$b;//Admin Redemption(Based on Max Redemption) (K)
                        if($a==100)
                        {
                            $n=$data['orderAmount']-1;

                        }
                    }
                    $balanceStorePoints=$g-$n;
                    if($balanceStorePoints<0)
                    {
                        $data['status'] = 0;
                        $data['message'] = "Reward points can't be redeemed for store";
                        return response($data);
                    }
                    $o=$n*$c;//Admin Redemption Amount(RS)(L)
                    $i=$orderAmount;
                    if($i>=$o)
                    {
                        $q=$i-$o;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = number_format((float)$q, 2, '.', '');;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    else
                    {
                        $q=$i;//Balance if Admin Redemption Only (P)
                        $data['totalReducableStoreAmount'] = 0.00;
                        if($a==100)
                        {
                            $data['totalReducableStoreAmount'] = number_format((float)$n, 2, '.', '');;

                        }

                    }
                    $wallet_log=new Trn_wallet_log();
                    $wallet_log->store_id=$store_id;
                    $wallet_log->customer_id=$request->customer_id;
                    $wallet_log->type='debit';
                    $wallet_log->points_debited= number_format((float)$n, 2, '.', '');;
                    $wallet_log->points_credited=null;
                    $wallet_log->save();
                    $data['wallet_id']=$wallet_log->wallet_log_id;
                                               
                    //$data['totalReducableStoreAmount'] =number_format((float)$q, 2, '.', '');;
                    $data['reducedStoreOrderAmount'] = $o;
                    $data['reducedAmountByStoreWalletPoints'] =number_format((float)$n, 2, '.', '');
                    $data['usedStorePoint'] = number_format((float)$n, 2, '.', '');
                    $data['balanceStorePoint'] = number_format((float)$balanceStorePoints, 2, '.', '');

                }
                $data['totalReducableAmount']=$data['totalReducableStoreAmount']+$data['totalReducableAdminAmount'];
                if($orderAmount>$data['totalReducableAmount'])
                {
                $r=$orderAmount-$data['totalReducableAmount'];
                }
                else
                {
                    $r=$orderAmount;
                }
            
                $data['remainingOrderAmount'] = number_format((float)$r, 2, '.', '');;
                return response($data);
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Order amount required";
            }
            

        // if($this->checkReducedAmount($orderAmount,$data['reducedAmountByWalletPoints'],$data['reducedAmountByStoreWalletPoints'])==0)
        // {
        //     $data['status'] = 0;
        //     $data['message'] = "Wallet reduced amount cannot be greater than order amount";
        //     return response($data);

        // }
        // else
        // {
        //     return response($data);

        // }
            
      
    }
    public function checkReducedAmount($order_amount,$reduced_admin_amount,$reduced_store_amount)
    {
        $orderAmt=$order_amount;
        $reducedAdminAmount=$reduced_admin_amount??0;
        $reducedStoreAmount=$reduced_store_amount??0;
        $totalReduceAmount=$reducedAdminAmount+$reducedStoreAmount;
        if($orderAmt<$totalReduceAmount)
        {
           return 0;

        }
        else
        {
            return 1;
        }

    }

    public function addToCart(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                if (isset($request->product_varient_id) && Mst_store_product_varient::where('product_varient_id',$request->product_varient_id)->where('variant_status','=',1)->first()) {
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

                        if (isset($request->attributes)) {
                            foreach ($request->attributes as $row) {
                                $attr_group_id =  $row->attr_group_id;
                                $attr_value_id =  $row->attr_value_id;

                                $varAttrCount = Trn_ProductVariantAttribute::where('product_varient_id', $request->product_varient_id)
                                    ->where('attr_group_id', $attr_group_id)
                                    ->where('attr_value_id', $attr_value_id)->count();
                                if ($varAttrCount <= 0) {
                                    $data['status'] = 0;
                                    $data['message'] = "Product unavailable";
                                    return $data;
                                }
                            }
                        }

                        $varProdu = Mst_store_product_varient::find($request->product_varient_id);
                
                        $proData = Mst_store_product::find($varProdu->product_id);

                        // if ($request->quantity > $varProdu->stock_count || $proData->product_status == 1) {  
                        //     $data['message'] = 'Stock/Product unavailable';
                        //     $data['status'] = 3;
                        //     return response($data);
                        // }
                       


                        if (Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status', 0)->where('product_varient_id', $request->product_varient_id)->first()) {
                            $cartItem = Trn_Cart::where('customer_id', $request->customer_id)
                                ->where('remove_status', 0)
                                ->where('product_varient_id', $request->product_varient_id);
                            // $cartItem->quantity = $request->quantity;
                            $cartItem->update(['quantity' => $request->quantity]);

                            $data['status'] = 1;
                            $data['message'] = "Product added to cart";
                            return response($data);
                        } else {

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
                        }
                    } else {
                        $data['status'] = 2;
                        $data['message'] = "Quantity invalid";
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

public function addToCartTest(Request $request)
{
    $proData = Mst_store_product::find(395);
    $currTime = date("G:i");
    $start = $proData->timeslot_start_time; //init the start time
    $end = $proData->timeslot_end_time; //init the end time
    //return $start;
   
    if ($currTime<$start || $currTime>$end)
    {
        //return 1;
        if($proData->is_timeslot_based_product==1)
        {
            return 'Product Unavailable. The product will be available from '.date('g:i A',strtotime($start)) .' to '.date('g:i A',strtotime($end));

        }
       

    }
  
}

    public function addToCartInternal(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                if (isset($request->product_varient_id) && Mst_store_product_varient::where('product_varient_id',$request->product_varient_id)->where('variant_status','=',1)->first()) {
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
                            // $productAvailableRes=$this->productAvailabilityCheck($varProdu->product_id);
                            // if($productAvailableRes==2)
                            // {
                            //     $data['message'] = 'Product Unavailable. The product will be available from'.." to "configured time".”';
                            //     $data['status'] = 3;
                            //     return response($data);

                            // }
                            $currTime = date("G:i");
                            $start = $proData->timeslot_start_time; //init the start time
                            $end = $proData->timeslot_end_time; //init the end time
                            //return $start;
                           
                            if ($proData->is_timeslot_based_product==1)
                            {
                                if($currTime<$start || $currTime>$end)
                                {
                                    $data['message'] = 'Product Unavailable. The product will be available from '.date('g:i A',strtotime($start)) .' to '.date('g:i A',strtotime($end));
                                    $data['status'] = 3;
                                    return response($data);
                                }
                               
                            }
                            if ($request->quantity > $varProdu->stock_count ) {  
                                $data['message'] = 'Stock unavailable';
                                $data['status'] = 3;
                                return response($data);
                            
                        }
                        if($proData->product_status == 0)
                        {
                            if($varProdu->is_base_variant==1)
                            {
                                $data['message'] = 'Product unavailable..Try again later';
                                $data['status'] = 3;
                                return response($data);

                            }

                        }
                        if($varProdu->variant_status==0)
                        {
                            $data['message'] = 'Product unavailable';
                            $data['status'] = 3;
                            return response($data);

                        }
                           
                    //if ($proData->service_type != 2) {  
                   
                            // recently visited products
                            //  $recVisStrRowCount = Trn_RecentlyVisitedProducts::where('customer_id',$request->customer_id)->count();
                            // if($recVisStrRowCount < 1)
                            // {
                            // Trn_RecentlyVisitedProducts::where('customer_id',$request->customer_id)->where('product_varient_id',$request->product_varient_id)->delete();
                    if($request->cart_type)
                    {
                        if($request->cart_type=='quick')
                        {
                                $rvs = new Trn_RecentlyVisitedProducts;
                                $rvs->customer_id = $request->customer_id;
                                $gData = Mst_store_product_varient::find($request->product_varient_id);
                                $rvs->store_id = $gData->store_id;
                                $rvs->product_id = $gData->product_id;
                                $rvs->product_varient_id = $request->product_varient_id;
                                $prData = Mst_store_product::find($gData->product_id);
                                $rvs->vendor_id = $prData->vendor_id;
                                $rvs->category_id = $prData->product_cat_id;
                                $rvs->sub_category_id = $prData->sub_category_id;
        
                                $rvs->visit_count = 1;
                                $rvs->save();
                        }
                    }
    
                           
                        
                        if (isset($varProdu)) { 
                            if ($request->quantity <= $varProdu->stock_count) {  //quantity shud be less dan current stock
               
                            $totcountInCart = Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status','=',0)->count();
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

                                        if(Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status','=',0)->count() > 0)
                                        {
                                            $data['CurrentCartCount'] = Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status','=',0)->count();
                                        }else{
                                            $data['CurrentCartCount'] = 0; 
                                        }
        
                                        $data['status'] = 1;
                                        $data['message'] = "Product added to cart";
                                        return response($data);
                            }else{
                                if ($getItem = Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status', 0)->where('product_varient_id', $request->product_varient_id)->first()) {

                                    $totalQuantity = $getItem->quantity + $request->quantity;

                                    $cartItem = Trn_Cart::where('customer_id', $request->customer_id)
                                        ->where('remove_status', 0)
                                        ->where('product_varient_id', $request->product_varient_id);
                                    $cartItem->update(['quantity' => $totalQuantity]);

                                    if(Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status','=',0)->count() > 0)
                                        {
                                            $data['CurrentCartCount'] = Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status','=',0)->count();
                                        }else{
                                            $data['CurrentCartCount'] = 0; 
                                        }
        
                                    $data['status'] = 1;
                                    $data['message'] = "Product added to cart";
                                    return response($data);
                                } else {

                                    if (Trn_Cart::where('customer_id', $request->customer_id)->where('store_id', '=', $request->store_id)->where('remove_status',0)->count() > 0 ) {  // to change 
                                        
                                        $proVarData = Mst_store_product_varient::find($request->product_varient_id);
                                        $cartItem = new Trn_Cart;
                                        $cartItem->store_id = $proVarData->store_id;
                                        $cartItem->customer_id = $request->customer_id;
                                        $cartItem->product_varient_id = $request->product_varient_id;
                                        $cartItem->product_id = $proVarData->product_id;
                                        $cartItem->quantity = $request->quantity;
                                        $cartItem->remove_status = 0;
                                        $cartItem->save();

                                        if(Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status','=',0)->count() > 0)
                                        {
                                            $data['CurrentCartCount'] = Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status','=',0)->count();
                                        }else{
                                            $data['CurrentCartCount'] = 0; 
                                        }

                                        
                                        $data['status'] = 1;
                                        $data['message'] = "Product added to cart";
                                        return response($data);
                                    }else{
                                        
                                        $data['status'] = 2;
                                        $data['message'] = "Product of another store in cart. do you want to remove?"; 
                                        return response($data);
                                    }
                                }

                            }

                                
                            } else {
                                $data['message'] = 'Stock unavailable';
                                $data['status'] = 3;
                                return response($data);
                            }
                    
                    } else {
                        $data['message'] = 'Product not found';
                        $data['status'] = 2;
                        return response($data);
                    }
                        
                    /*}else{
                        $data['status'] = 2;
                        $data['message'] = "Cannot add service product to cart";
                    }*/

                    
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

    //internal delete all current items in cart
    public function removeAllStoreItems(Request $request)
    {
        $data = array();
        try {
            if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
               
                if($request->cart_type)
                {
                    if($request->cart_type=='quick')
                    {
                            $rvs = new Trn_RecentlyVisitedProducts;
                            $rvs->customer_id = $request->customer_id;
                            $gData = Mst_store_product_varient::find($request->product_varient_id);
                            $rvs->store_id = $gData->store_id;
                            $rvs->product_id = $gData->product_id;
                            $rvs->product_varient_id = $request->product_varient_id;
                            $prData = Mst_store_product::find($gData->product_id);
                            $rvs->vendor_id = $prData->vendor_id;
                            $rvs->category_id = $prData->product_cat_id;
                            $rvs->sub_category_id = $prData->sub_category_id;
    
                            $rvs->visit_count = 1;
                            $rvs->save();
                    }
                }
                    if($request->remove_flag == 1)
                    {

                    //all items related to customer has to be removed from the table and new product shoudl be added
                    //remove all products of the previous store
                    Trn_Cart::where('customer_id', $request->customer_id)->delete();
                   $getlatestCartCount =  Trn_Cart::where('customer_id', $request->customer_id)->where('remove_status',0)->count();
                   
                    //check new product existance
                    $varProdu = Mst_store_product_varient::find($request->product_varient_id);
                
                    $proData = Mst_store_product::find($varProdu->product_id);
                    //if ($proData->service_type != 2) {
                        
                        if (isset($varProdu)) {
                            
                            if ($request->quantity <= $varProdu->stock_count) {
                                
                                //add the product to cart
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
   
                            } else {
                                $data['message'] = 'Stock unavailable';
                                $data['status'] = 3;
                                return response($data);
                            }
                        } else {
                        $data['message'] = 'Product not found';
                        $data['status'] = 2;
                        return response($data);
                        }
                        
                   /* }else{
                        $data['status'] = 2;
                        $data['message'] = "Cannot add service product to cart";
                    }*/
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
                                'mst_store_products.display_flag',
                                'mst_store_products.is_timeslot_based_product',
                                'mst_store_products.timeslot_start_time',
                                'mst_store_products.timeslot_end_time',
                                'mst_store_product_varients.product_varient_id',
                                'mst_store_product_varients.variant_name',
                                'mst_store_product_varients.product_varient_price',
                                'mst_store_product_varients.product_varient_offer_price',
                                'mst_store_product_varients.product_varient_base_image',
                                'mst_store_product_varients.stock_count',
                                'mst_store_product_varients.store_id',
                                'mst_store_product_varients.is_base_variant',
                                'mst_store_product_varients.variant_status',
                                
                            )
                            ->where('mst_store_product_varients.product_varient_id', $cartData->product_varient_id)
                            ->whereNotNull('mst_store_product_varients.product_varient_price')
                            ->whereNotNull('mst_store_product_varients.product_varient_offer_price')
                            ->first();
                        @$cartData->productData->product_base_image = '/assets/uploads/products/base_product/base_image/' . @$cartData->productData->product_base_image;
                    if(@$cartData->productData->product_varient_base_image!=NULL)
                    {
                        @$cartData->productData->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . @$cartData->productData->product_varient_base_image;

                    }
                    else
                    {
                        @$cartData->productData->product_varient_base_image =@$cartData->productData->product_base_image;

                    }
                        
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
                            ->update(['remove_status' =>  1]); //1=deleted
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
    public function storePointUntick(Request $request)
    {
        $data=array();
        $store_id=$request->store_id;
        $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
        if($wallet_log_first->first())
        {
            $wallet_log_first->first()->delete();
        }
        $data['status']=1;
        $data['message']="Data deleted";
        return response($data);


      

    }
    public function adminPointUntick(Request $request)
    {
        $data=array();
        $store_id=$request->store_id;
        $wallet_log_first=Trn_wallet_log::where('type','debit')->where('customer_id', $request->customer_id)->where('store_id',$store_id)->whereNull('order_id');
        if($wallet_log_first->first())
        {
            $wallet_log_first->first()->delete();
        }
        $data['status']=1;
        $data['message']="Data deleted";
        return response($data);


        
    }
}
