<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Crypt;
use  Carbon\Carbon;
use Validator;

use App\Models\admin\Mst_store;
use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_Tax;
use App\User;
use App\Models\admin\Trn_ReviewsAndRating;
use App\Models\admin\Mst_Subadmin_Detail;
use App\Models\admin\Trn_StoreTimeSlot;
use App\Models\admin\Trn_ProductVariantAttribute;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_points_redeemed;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_setting;
use App\Models\admin\Trn_StoreBankData;
use Auth;

class Helper
{
    
    public static function get_guard()
    {
        if(Auth::guard('web')->check())
            {return "web";}
        elseif(Auth::guard('store')->check())
            {return "store";}
        elseif(Auth::guard('api')->check())
            {return "api";}
        elseif(Auth::guard('customer')->check())
            {return "customer";}
        elseif(Auth::guard('api-customer')->check())
            {return "api-customer";}
            else{
                return "";
            }
    }



    public static function findStoreName($store_id)
    {
        return $sname =  Mst_store::find($store_id)->store_name;
    }

    public static function findStorePhone($store_id)
    {
        return $sname =  Mst_store::find($store_id)->store_mobile;
    }

    public static function findCustomerName($cusId)
    {
        $cusData = Trn_store_customer::find($cusId);
        return $cusData->customer_first_name . " " . $cusData->customer_last_name;
    }

    public static function findCustomerPhone($cusId)
    {
        $cusData = Trn_store_customer::find($cusId);

        return $cusData->customer_mobile_number;
    }

    public static function findRewardPoints($cusId)
    {
        $totalCustomerRewardsCount = Trn_customer_reward::where('customer_id', $cusId)->where('reward_point_status', 1)->sum('reward_points_earned');
        $totalusedPoints = Trn_store_order::where('customer_id', $cusId)->whereNotIn('status_id', [5])->sum('reward_points_used');
        $redeemedPoints = Trn_points_redeemed::where('customer_id', $cusId)->sum('points');
        $customerRewardsCount = ($totalCustomerRewardsCount - $totalusedPoints) - $redeemedPoints;
        return number_format($customerRewardsCount, 2);
    }

    public static function onBoardingStatus($store_id)
    {
        $isProfileFilled = Helper::isProfileFilled($store_id);
        $isServiceAreaSet = Helper::isServiceAreaSet($store_id);
        $isWorkingDaysSet = Helper::isWorkingDaysSet($store_id);
        $s = 1;

        if (($isProfileFilled == 1) && ($isServiceAreaSet != 1) && ($isWorkingDaysSet != 1)) {
            $s = 2;
        }

        if (($isProfileFilled == 1) && ($isServiceAreaSet == 1) && ($isWorkingDaysSet != 1)) {
            $s = 3;
        }

        if (($isProfileFilled == 1) && ($isServiceAreaSet == 1) && ($isWorkingDaysSet == 1)) {
            $s = 4;
        }

        // if ($isProfileFilled == 1) {
        //     $s = 2;
        //     if ($isServiceAreaSet == 1) {
        //         $s = 3;
        //         if ($isWorkingDaysSet == 1) {
        //             $s = 5;
        //         }
        //     }
        // }
        return $s;
    }


    // public static function onBoardingStatus($store_id)
    // {
    //     $isProfileFilled = Helper::isProfileFilled($store_id);
    //     $isServiceAreaSet = Helper::isServiceAreaSet($store_id);
    //     $isWorkingDaysSet = Helper::isWorkingDaysSet($store_id);
    //     $s = 1;

    //     if ($isWorkingDaysSet == 1) {
    //         $s = 4;
    //         if ($isServiceAreaSet != 1) {
    //             $s = 3;
    //             if ($isProfileFilled != 1) {
    //                 $s = 2;
    //             }
    //         }
    //     }

    //     if ($isProfileFilled == 1) {
    //         $s = 2;
    //         if ($isServiceAreaSet == 1) {
    //             $s = 3;
    //             if ($isWorkingDaysSet == 1) {
    //                 $s = 4;
    //             }
    //         }
    //     }


    //     return $s;
    // }
    public static function isBankDataFilled($store_id)
    {
        $sBankDAta = Trn_StoreBankData::where('store_id', $store_id)->where('status', 1)->count();
        if ($sBankDAta > 0)
            return 1;
        else
            return 0;
    }

    public static function isProfileFilled($store_id)
    {
        $store =  Mst_store::find($store_id);
        if (
            !isset($store->store_contact_person_name) ||
            !isset($store->store_contact_person_phone_number) ||
            !isset($store->store_country_id) ||
            !isset($store->store_state_id) ||
            !isset($store->store_district_id) ||
            !isset($store->town) ||
            !isset($store->place) ||
            // !isset($store->store_pincode) ||
            !isset($store->store_primary_address)

        ) {
            return 0;
        } else {
            return 1;
        }
    }


    public static function isServiceAreaSet($store_id)
    {
        $store =  Mst_store::find($store_id);

        if (!isset($store->service_area) || ($store->service_area <= 0)) {
            return 0;
        } else {
            $serviceData =  Trn_store_setting::where('store_id', $store_id)->count();
            if ($serviceData <= 0) {
                return 0;
            }
        }
        return 1;
    }

    public static function isWorkingDaysSet($store_id)
    {
        $storeData = Trn_StoreTimeSlot::where('store_id', $store_id)->get();
        $c = 0;
        foreach ($storeData as $row) {
            if (isset($row->time_start) && isset($row->time_end)) {
                $c++;
            }
        }
        //  dd($storeData);

        if ($c > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function findSubAdminName($store_id)
    {
        $userId =  Mst_store::find($store_id)->subadmin_id;
        $uData =  User::find($userId);
        if (isset($uData->admin_name))
            return @$uData->admin_name;
        else
            return  '---';

        // if (isset($userId)) {
        //     if (User::find($userId)->admin_name) {
        //         return User::find($userId)->admin_name;
        //     } else {
        //         return '---';
        //     }
        // }
        // return '---'; 

    }


    public static function storeSubadminPhone($store_id)
    {
        $store_data = Mst_store::find($store_id);
        $subadminData = Mst_Subadmin_Detail::where('subadmin_id', $store_data->subadmin_id)->first();
        if (isset($subadminData->phone))
            return @$subadminData->phone;
        else
            return '0';
    }

    public static function findStoreDataFilled($store_id)
    {
        $returnData = 1;
        $sData = Mst_store::find($store_id);
        return $returnData;
    }


    public static function findHoliday($store_id)
    {
        $timeslotdata = Trn_StoreTimeSlot::where('store_id', $store_id)
            ->where('day', Carbon::now()->format('l'))
            ->whereTime('time_start', '<=', Carbon::now()->format('H:i'))
            ->whereTime('time_end', '>', Carbon::now()->format('H:i'))
            ->exists();
        return $timeslotdata;
    }

    public static function findServiceVariant($product_id)
    {
        $proData = Mst_store_product::find($product_id);
        $productVar =  Mst_store_product_varient::where('product_id', $product_id)->where('is_removed', 0)->first();
        if ($proData->service_type == 1)
            return $productVar->product_varient_id;
        else
            return '0';
    }

    public static function varAttrStatus($product_id)
    {

        // $proVaattrrCount = Trn_ProductVariantAttribute::where('product_varient_id', $product->product_varient_id)->count();
        // if ($proVaattrrCount < 1) {
        //     return 0;
        //     } else {
        //     return 1;
        // }

        return 1;
    }

    public static function isBaseVariant($product_id)
    {
        $v = Helper::variantCount($product_id);
        if ($v < 1) {
            return 0;
        } elseif ($v == 1) {
            $proCoubt = Mst_store_product_varient::where('product_id', $product_id)
                ->where('is_removed', 0)
                ->where('is_base_variant', 1)
                ->where('stock_count', '>', 0)
                ->count();
            if ($proCoubt > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    
    public static function varAttrCount($product_varient_id)
    {
       $arrtVal  = Trn_ProductVariantAttribute::where('product_varient_id', $product_varient_id)
                    ->count();
                return $arrtVal;
    }
    

    public static function attrCount($product_id)
    {
        $v = Helper::variantCount($product_id);
        if ($v < 1) {
            return 0;
        } elseif ($v == 1) {
            $proCoubt = Mst_store_product_varient::where('product_id', $product_id)
                ->where('is_removed', 0)
                ->where('is_base_variant', 1)
                ->where('stock_count', '>', 0)
                ->first();

            if (isset($proCoubt)) {
                $arrtVal  = Trn_ProductVariantAttribute::where('product_varient_id', $proCoubt->product_varient_id)
                    ->count();
                return $arrtVal;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function variantCount($product_id)
    {
        $proCoubt = Mst_store_product_varient::where('product_id', $product_id)
            ->where('is_removed', 0)
            ->where('stock_count', '>', 0)
            ->count();
        if ($proCoubt)
            return $proCoubt;
        else
            return 0;
    }

    public static function productStock($product_id)
    {
        $stockSum = Mst_store_product_varient::where('product_id', $product_id)->where('is_removed', 0)->sum('stock_count');
        if ($stockSum)
            return $stockSum;
        else
            return 0;
    }

    public static function productRating($product_id)
    {
        $sumRating = Trn_ReviewsAndRating::where('product_id', $product_id)->where('isVisible', 1)->sum('rating');
        $countRating = Trn_ReviewsAndRating::where('product_id', $product_id)->where('isVisible', 1)->count();
        if ($countRating == 0) {
            $countRating = 1;
        }
        $ratingData = $sumRating / $countRating;
        $rating = number_format((float)$ratingData, 2, '.', '');

        if ($rating)
            return $rating;
        else
            return 0;
    }

    public static function productRatingCount($product_id)
    {
        $countRating = Trn_ReviewsAndRating::where('product_id', $product_id)->where('isVisible', 1)->count();

        if ($countRating)
            return $countRating;
        else
            return 0;
    }

    public static function storeSuperadminPhone($store_id)
    {
        return '0101010101';
    }




    public static function storeRating($store_id)
    {
        $sumRating = Trn_ReviewsAndRating::where('store_id', $store_id)->where('isVisible', 1)->sum('rating');
        $countRating = Trn_ReviewsAndRating::where('store_id', $store_id)->where('isVisible', 1)->count();

        if ($countRating == 0) {
            $countRating = 1;
        }
        $ratingData = $sumRating / $countRating;

        return number_format((float)$ratingData, 2, '.', '');
    }


    public static function storeRatingCount($store_id)
    {
        return $countRating = Trn_ReviewsAndRating::where('store_id', $store_id)->where('isVisible', 1)->count();
    }


    public static function distanceFinder($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }


    public static function orderTotalDiscount($order_id)
    {
        $orderItems = Trn_store_order_item::select('product_id', 'quantity', 'unit_price', 'product_varient_id')->where('order_id', $order_id)->get();
        $orderItemsCount = Trn_store_order_item::where('order_id', $order_id)->count();
        $totalDis = 0;
        if ($orderItemsCount > 0) {
            foreach ($orderItems as $item) {
                $product_varient = Mst_store_product_varient::find($item->product_varient_id);
                $totalDis = $totalDis + ((@$product_varient->product_varient_price - @$product_varient->product_varient_offer_price) * $item->quantity);
            }
            return $totalDis;
        } else {
            return 0;
        }
    }

    public static function orderTotalTax($order_id)
    {
        //  $orderTotalAmount = Trn_store_order_item::where('order_id', $order_id)->sum('total_amount');

        $orderTotalTax = Trn_store_order_item::where('order_id', $order_id)->sum('tax_amount');
        if (isset($orderTotalTax) && ($orderTotalTax != 0)) {
            $orderItems = Trn_store_order_item::select('product_id', 'quantity', 'unit_price', 'product_varient_id')->where('order_id', $order_id)->get();
            $totalTax = 0;
            foreach ($orderItems as $item) {
                $productData = Mst_store_product::find($item->product_id);
                if (isset($productData->tax_id) && ($productData->tax_id != 0)) {

                    $taxData = Mst_Tax::find($productData->tax_id);

                    $product_varient = Mst_store_product_varient::find($item->product_varient_id);
                    //return $product_varient;
                    $tax = $item->quantity * (@$product_varient->product_varient_offer_price * @$taxData->tax_value / (100 + @$taxData->tax_value));

                    //  return   $tax = (@$taxData->tax_value / 100) * ($item->quantity * $item->unit_price);
                    $totalTax = $totalTax + $tax;
                }
            }
            return number_format((float)$totalTax, 2, '.', '');
        } elseif ($orderTotalTax == 0) {
            $orderItems = Trn_store_order_item::select('product_id', 'quantity', 'unit_price')->where('order_id', $order_id)->get();
            $totalTax = 0;
            foreach ($orderItems as $item) {
                $productData = Mst_store_product::find($item->product_id);
                if (isset($productData->tax_id) && ($productData->tax_id != 0)) {
                    $taxData = Mst_Tax::find($productData->tax_id);

                    $tax = (@$taxData->tax_value / 100) * ($productData->quantity * $productData->unit_price);
                    $totalTax = $totalTax + $tax;
                }
            }
            return  number_format((float)$totalTax, 2, '.', '');;
        } else {
            return  '0.0';
        }
    }


    // public static function orderTotalDiscount($order_id)
    // {
    //     $orderItemsDiscountSum = Trn_store_order_item::where('order_id', $order_id)->sum('discount_amount');
    //     if (isset($orderItemsDiscountSum))
    //         return  $orderItemsDiscountSum;
    //     else
    //         return 0;
    // }

    // public static function orderTotalTax($order_id)
    // {
    //     //  $orderTotalAmount = Trn_store_order_item::where('order_id', $order_id)->sum('total_amount');

    //     $orderTotalTax = Trn_store_order_item::where('order_id', $order_id)->sum('tax_amount');
    //     if (isset($orderTotalTax) && ($orderTotalTax != 0)) {
    //         $orderItems = Trn_store_order_item::select('product_id', 'quantity', 'unit_price')->where('order_id', $order_id)->get();
    //         $totalTax = 0;
    //         foreach ($orderItems as $item) {
    //             $productData = Mst_store_product::find($item->product_id);
    //             if (isset($productData->tax_id) && ($productData->tax_id != 0)) {
    //                 $taxData = Mst_Tax::find($productData->tax_id);

    //                 $tax = (@$taxData->tax_value / 100) * ($item->quantity * $item->unit_price);
    //                 $totalTax = $totalTax + $tax;
    //             }
    //         }
    //         return $totalTax;
    //     } elseif ($orderTotalTax == 0) {
    //         $orderItems = Trn_store_order_item::select('product_id', 'quantity', 'unit_price')->where('order_id', $order_id)->get();
    //         $totalTax = 0;
    //         foreach ($orderItems as $item) {
    //             $productData = Mst_store_product::find($item->product_id);
    //             if (isset($productData->tax_id) && ($productData->tax_id != 0)) {
    //                 $taxData = Mst_Tax::find($productData->tax_id);

    //                 $tax = (@$taxData->tax_value / 100) * ($productData->quantity * $productData->unit_price);
    //                 $totalTax = $totalTax + $tax;
    //             }
    //         }
    //         return $totalTax;
    //     } else {
    //         return  '0.0';
    //     }
    // }



    public static function customerNotification($device_id, $title, $body,$clickAction, $type)
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



    public static function deliveryBoyNotification($device_id, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = 'AAAARrd44xk:APA91bFzEarq0xuLOOD2nnkMrB102CHEPSZXV6LZZnQsMwUSVeJPSXrQ9Vxg_3wP-eXrypj5Kq8GpXn6Kig3Rq84C4q63J4LV-dtDEHRdLiv5saU7ZPBrnw-rGoQc3buW93r9xqpoyJv';
        $fields = array(
            'to' => $device_id,
            'notification' => array('title' => $title, 'body' => $body, 'sound' => 'default',''),
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


    public static function storeNotification($device_id, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = 'AAAAnXagbe8:APA91bEqMgI9Wb_psiCzKPNCQcoFt3W7RwG08oucA_UHwMjTBIbLyalZgMnigItD-0e8SDrWPfxHrT4g5zlfXHovUITXLuB32RdWp3abYyqJh2xIy_tAsGuPJJdnV5sNGxrnrrnExYYm';
        $fields = array(
            'to' => $device_id,
            'notification' => array('title' => $title, 'body' => $body, 'sound' => 'default'),
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


    public static function storeNotifyWeb($device_id, $title, $body)
    {

        $SERVER_API_KEY = 'AAAAZ5VSsVE:APA91bEmc0gaD9tE94DJOaFpQHA0NTZtGMlR-Fx_Tz9wJcwn3rIQKG5YPgxHkbiu-3SrcsHG-IWDWfNhes0krQr4L8jazCQCACFn_nKXMVByZgzeYTMKFKl-1xwC43Wg_g0KHbYWNbjG';

        $data = [
            "to" => $device_id,
            'notification' => array(
                'title' => $title, 'body' => $body, 'sound' => "default",
                'icon' => "https://yellowstore.in/assets/uploads/favicon.png"
            ),

        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        return $response;
    }


    public static function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$latitudeFrom.",".$longitudeFrom."&destinations=".$latitudeTo.",".$longitudeTo."&mode=driving&language=en-EN&key=AIzaSyAKYUXBDwnBHpAW7OWVEfqp4L-ZCxmTJjw";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($response, true);
                $dist = $data['rows'][0]['elements'][0]['distance']['text'];
                $time = $data['rows'][0]['elements'][0]['duration']['text'];
                return $dist;
        
        // // convert from degrees to radians
        // $latFrom = deg2rad($latitudeFrom); //store  lat
        // $lonFrom = deg2rad($longitudeFrom); //stor long
        // $latTo = deg2rad($latitudeTo); //customer lat
        // $lonTo = deg2rad($longitudeTo); // customer long
        

        // $latDelta = $latTo - $latFrom;
        // $lonDelta = $lonTo - $lonFrom;

        // $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        //     cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        // $dist = $angle * $earthRadius;

        // //dd(round($actualdist,2));
    
        // return number_format((float)$dist, 2, '.', '');

        
    }

 

    public static function haversineGreatCircleDistance2($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        //convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);


        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        $dist = $angle * $earthRadius;

        dd($latFrom, $lonFrom, $latTo, $lonTo, $latDelta, $lonDelta, $angle, $dist);
        return number_format((float)$dist, 2, '.', '');
    }



    public static function subAdminName($storeAdminId)
    {
        $storeAdminData = User::find($storeAdminId);
        if (isset($storeAdminData->name))
            return $storeAdminData->name;
        else
            return '---';
    }


    public static function adminName($storeAdminId)
    {
        $storeAdminData = Trn_StoreAdmin::find($storeAdminId);
        if ($storeAdminData->role_id == 0) {
            $storeData = Mst_store::find($storeAdminData->store_id);
            return $storeData->store_name;
        } else {
            return $storeAdminData->admin_name;
        }
    }

    public static function default_user_image()
    {
        return '/assets/user.png';
    }

    public static function default_video_image()
    {
        return '/assets/uploads/video_images/download.png';
    }

    public static function default_subcat_image()
    {
        return '/assets/uploads/video_images/download.png';
    }


    public static function default_store_image()
    {
        // return '/assets/uploads/store_images/images/1619507043.png';
        return '/assets/uploads/noStoreImage.jpg';
    }


    public static function validateStore($valid)
    {
        $validate = Validator::make(
            $valid,
            [
                'store_name'                       => 'required',
                // 'store_contact_person_name'        => 'required',
                // 'store_contact_person_phone_number'=> 'required',
                // 'store_contact_address'             => 'required',
                // 'store_country_id'                  => 'required|numeric',
                // 'store_state_id'                    => 'required|numeric',
                // 'store_district_id'                  => 'required|numeric',
                // 'store_town_id'                           => 'required|numeric',
                // 'store_place'                             => 'required',
                'business_type_id'                  => 'required|numeric',
                // 'store_username'                  => 'required',
                'store_mobile'                     => 'required|unique:trn__store_admins|numeric',
                'password'                         => 'required|min:5|same:password_confirmation',


            ],
            [
                'store_name.unique'                => 'Store Name Exists',
                'store_name.required'                => 'Store Name Field required',
                'store_contact_person_name' => 'Contact Person Name Field required',
                'store_contact_address' => 'Address Field required',
                'store_country_id' => 'Country Field is  required',
                'store_state_id' => 'State Field is  required',
                'store_district_id' => 'District Field is  required',
                'store_town_id' => 'Town Field is  required',
                'store_place' => 'Place Field is  required',
                'business_type_id' => 'Buisness Type Field is  required',
                'store_contact_person_phone_number.required' => ' Mobile required',
                'store_username' => 'Store Username is required',
                'password.required'                  => 'Store password required',
                'store_mobile.required'                  => 'Store mobile number required',
                'store_mobile.unique'                  => 'Store mobile number already exists ',

            ]
        );
        return $validate;
    }



    public static function validateCustomer($valid)
    {
        $validate = Validator::make(
            $valid,
            [
                'customer_name' => 'required',
                //  'customer_email'    => 'email',
                'customer_mobile_number'    => 'required|unique:trn_store_customers|numeric',
                'password'  => 'required|min:5|same:password_confirmation',


            ],
            [
                'customer_name.required'                => 'Customer name required',
                'customer_mobile_number.unique'                  => 'Mobile number already exists ',
                'customer_email.email'                  => 'Invalid email ',
                'password.required'                  => 'Password required ',

            ]
        );
        return $validate;
    }
}
