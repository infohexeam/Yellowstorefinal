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

use App\Models\admin\Trn_store_customer;



class Helper
{

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

    public static function findSubAdminName($store_id)
    {
        $userId =  Mst_store::find($store_id)->subadmin_id;
        return $uData =  User::find($userId);
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
        $productVar =  Mst_store_product_varient::where('product_id', $product_id)->first();
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

    public static function productStock($product_id)
    {
        $stockSum = Mst_store_product_varient::where('product_id', $product_id)->sum('stock_count');
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
        $orderItemsDiscountSum = Trn_store_order_item::where('order_id', $order_id)->sum('discount_amount');
        if (isset($orderItemsDiscountSum))
            return  $orderItemsDiscountSum;
        else
            return 0;
    }

    public static function orderTotalTax($order_id)
    {
        //  $orderTotalAmount = Trn_store_order_item::where('order_id', $order_id)->sum('total_amount');

        $orderTotalTax = Trn_store_order_item::where('order_id', $order_id)->sum('tax_amount');
        if (isset($orderTotalTax) && ($orderTotalTax != 0)) {
            return  $orderTotalTax;
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
            return $totalTax;
        } else {
            return  '0.0';
        }
    }



    public static function customerNotification($device_id, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = 'AAAA09gixf4:APA91bFiBdhtMnj2UBtqSQ9YlZ_uxvdOOOzE-otA9Ja2w0cFUpX230Xv0Yi87owPBlFDp1H02FWpv4m8azPsuMmeAmz0msoeF-1Cxx0iVpDSOjYBTCWxzUYT8tKTuUvLb08MDsRXHbgM';
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



    public static function deliveryBoyNotification($device_id, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = 'AAAARrd44xk:APA91bFzEarq0xuLOOD2nnkMrB102CHEPSZXV6LZZnQsMwUSVeJPSXrQ9Vxg_3wP-eXrypj5Kq8GpXn6Kig3Rq84C4q63J4LV-dtDEHRdLiv5saU7ZPBrnw-rGoQc3buW93r9xqpoyJv';
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
            'notification' => array('title' => $title, 'body' => $body),

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
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return round($angle * $earthRadius);
    }


    public static function subAdminName($storeAdminId)
    {
        $storeAdminData = User::find($storeAdminId);
        if (isset($storeAdminData->name))
            return $storeAdminData->name;
        else
            return '--';
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
                'store_name'                       => 'required|unique:mst_stores',
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
