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
use App\Helpers\Helper;
use App\Models\admin\District;
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
use App\Models\admin\Trn_RecentlyVisitedStore;
use App\Models\admin\Trn_StoreTimeSlot;

use App\Models\admin\Mst_product_image;
use App\Models\admin\Trn_GlobalProductImage;

use App\Models\admin\Mst_CustomerAppBanner;
use App\Models\admin\Mst_store_images;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Trn_MostVisitedProductsByCustomer;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Trn_customerAddress;
use App\Models\admin\Trn_ReviewsAndRating;
use App\Models\admin\Trn_RecentlyVisitedProducts;

class BusinessTypeController extends Controller
{
    public function test(Request $request)
    {

        $dist = Helper::haversineGreatCircleDistance2($request->F_latitude, $request->F_longitude, $request->L_latitude, $request->L_longitude);
        dd($dist);

        $RefundOrderDatas = Trn_store_order::where('isRefunded', 1)
            ->get();

        foreach ($RefundOrderDatas as $row) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.cashfree.com/api/v1/refundStatus/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('appId' => '165253d13ce80549d879dba25b352561', 'secretKey' => 'bab0967cdc3e5559bded656346423baf0b1d38c4', 'refundId' => '11644414'),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $refundResponseFinal = json_decode($response, true);
            if ($refundResponseFinal['refund'][0]['processed'] == "YES") {
                Trn_store_order::where('order_id', $row->order_id)->update([
                    "isRefunded" => 2,
                    "refundStatus" => "Success",
                ]);
            }
        }

        $data = array();
        try {

            $latitude = '11.2541474';
            $longitude = '75.77008459999999';
            $stores          =       DB::table("mst_stores")->join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id');
            $stores         = $stores->where('trn__store_admins.role_id', 0);
            $stores         = $stores->where('mst_stores.online_status', 1);
            $stores         = $stores->where('trn__store_admins.store_account_status', 1);
            $stores          =       $stores->select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                    * cos(radians(mst_stores.latitude)) * cos(radians(mst_stores.longitude) - radians(" . $longitude . "))
                                    + sin(radians(" . $latitude . ")) * sin(radians(mst_stores.latitude))) AS distance"));
            $stores          =       $stores->having('distance', '<', 24);
            $stores          =       $stores->orderBy('distance', 'asc');
            $stores          =       $stores->get();
            dd($stores);

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }


    public function BTHomePage(Request $request)
    {
        $data = array();
        try {
            if (isset($request->business_type_id) && Mst_business_types::find($request->business_type_id)) {
                if ($request->customer_id == 0) {
                    $business_type_id = $request->business_type_id;

                    $data['sliderImages'] =  Mst_CustomerAppBanner::select('banner_id', 'image', 'town_id', 'status', 'default_status')->where('default_status', 1)->where('store_id', 0)->where('status', 1)->get();
                    foreach ($data['sliderImages'] as $img) {
                        $img->image = '/assets/uploads/customer_banner/' . $img->image;
                    }

                    // $data['categoriesList'] = Mst_categories::select('category_id','business_type_id','category_description','category_icon','category_name')->where('business_type_id', $business_type_id)->where('category_status', 1)->orderBy('category_id', 'DESC')->get();
                    // foreach($data['categoriesList'] as $cat)
                    // {
                    //   $cat->category_icon = '/assets/uploads/business_type/icons/'.$cat->category_icon;
                    // }


                    $latitude = $request->latitude;
                    $longitude = $request->longitude;

                    $productData = Mst_store_product::join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id');

                    if (isset($latitude) && ($longitude)) {
                        $productData = $productData->select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                                * cos(radians(mst_stores.latitude)) * cos(radians(mst_stores.longitude) - radians(" . $longitude . "))
                                                + sin(radians(" . $latitude . ")) * sin(radians(mst_stores.latitude))) AS distance"));
                        $productData = $productData->orderBy('distance');
                    }
                    $productData = $productData->where('mst_store_products.product_status', 1)
                        ->where('mst_store_products.show_in_home_screen', 1)
                        ->where('mst_stores.business_type_id', $business_type_id)
                        ->get();

                    $productDataFinal = array();
                    $stockCount = 0;
                    foreach ($productData as $offerProduct) {

                        if (Helper::productStock($offerProduct->product_id) > 0) {
                            $offerProduct->product_base_image = '/assets/uploads/products/base_product/base_image/' . $offerProduct->product_base_image;
                            $storeData = Mst_store::find($offerProduct->store_id);
                            $offerProduct->store_name = $storeData->store_name;
                            $offerProduct->rating = Helper::productRating($offerProduct->product_id);
                            $offerProduct->ratingCount = Helper::productRatingCount($offerProduct->product_id);

                            $offerProduct->productStock = Helper::productStock($offerProduct->product_id);
                            $offerProduct->variantCount = Helper::variantCount($offerProduct->product_id);
                            $offerProduct->isBaseVariant = Helper::isBaseVariant($offerProduct->product_id);
                            $offerProduct->attrCount = Helper::attrCount($offerProduct->product_id);

                            $productDataFinal[] =   $offerProduct;
                        }
                    }
                    $data['offerProducts']  =    $productDataFinal;


                    $data['recentlyVisitedStores'] = [];

                    $nearByStores =  Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
                        ->where('trn__store_admins.role_id', 0)->where('mst_stores.online_status', 1)
                        ->where('trn__store_admins.store_account_status', 1)
                        ->where('mst_stores.business_type_id', $business_type_id);

                    if (isset($latitude) && ($longitude)) {
                        $nearByStores = $nearByStores->select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                                * cos(radians(mst_stores.latitude)) * cos(radians(mst_stores.longitude) - radians(" . $longitude . "))
                                                + sin(radians(" . $latitude . ")) * sin(radians(mst_stores.latitude))) AS distance"));
                        $nearByStores =   $nearByStores->having('distance', '<', 10);
                    }

                    $nearByStores   = $nearByStores->limit(10)->get();
                    $nearStoreArray[] = 0;

                    $nearByStoresdataf = array();
                    foreach ($nearByStores as $nearByStore) {
                        $nearStoreArray[] = $nearByStore->store_id;

                        $timeslotdata = Helper::findHoliday($nearByStore->store_id);
                        if ($timeslotdata == true) {


                            if (isset($nearByStore->profile_image)) {
                                $nearByStore->store_image =  '/assets/uploads/store_images/images/' . $nearByStore->profile_image;
                            } else {
                                $nearByStore->store_image =  Helper::default_store_image();
                            }

                            if (isset($nearByStore->store_district_id))
                                $nearByStore->district_name = District::find($nearByStore->store_district_id)->district_name;
                            else
                                $nearByStore->district_name = '';

                            $storeProductData2 = Mst_store_product::select('product_cat_id')->where('store_id', '=', $nearByStore->store_id)->orderBy('product_id', 'DESC')->get()->unique('product_cat_id')->pluck('product_cat_id')->toArray();
                            $catData2 = Mst_categories::whereIn('category_id', $storeProductData2)->where('category_status', 1)->get()->pluck('category_name')->toArray();
                            $catString2 = implode(', ', @$catData2);
                            if (isset($catString2))
                                $string2 = substr(@$catString2, 0, 27);
                            else
                                $string2 = null;

                            $nearByStore->categories =  @$string2;
                            // $nearByStore->rating = number_format((float)4.20, 1, '.', '');
                            // $nearByStore->ratingCount = 120;

                            $nearByStore->rating = Helper::storeRating($nearByStore->store_id);
                            $nearByStore->ratingCount = Helper::storeRatingCount($nearByStore->store_id);
                            $nearByStoresdataf[] = $nearByStore;
                        }
                    }

                    $data['nearByStores'] = $nearByStoresdataf;

                    // other stores

                    $otherStores =  Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
                        ->where('trn__store_admins.role_id', 0)->where('mst_stores.online_status', 1)
                        ->where('mst_stores.business_type_id', $business_type_id)
                        ->where('trn__store_admins.store_account_status', 1)
                        ->whereNotIn('mst_stores.store_id', $nearStoreArray);


                    if (isset($latitude) && ($longitude)) {
                        $otherStores = $otherStores->select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                                * cos(radians(mst_stores.latitude)) * cos(radians(mst_stores.longitude) - radians(" . $longitude . "))
                                                + sin(radians(" . $latitude . ")) * sin(radians(mst_stores.latitude))) AS distance"));
                        $otherStores =   $otherStores->orderBy('distance');
                    }

                    $otherStoress = $otherStores->get();
                    $otherStoresTwo = array();


                    foreach ($otherStoress as $otherStores) {


                        $timeslotdata = Helper::findHoliday($nearByStore->store_id);
                        if ($timeslotdata == true) {

                            if (isset($otherStores->profile_image)) {
                                $otherStores->store_image =  '/assets/uploads/store_images/images/' . $otherStores->profile_image;
                            } else {
                                $otherStores->store_image =  Helper::default_store_image();
                            }

                            if (isset($otherStores->store_district_id))
                                $otherStores->district_name = District::find($otherStores->store_district_id)->district_name;
                            else
                                $otherStores->district_name = '';

                            $storeProductData2 = Mst_store_product::select('product_cat_id')->where('store_id', '=', $otherStores->store_id)->orderBy('product_id', 'DESC')->get()->unique('product_cat_id')->pluck('product_cat_id')->toArray();
                            $catData2 = Mst_categories::whereIn('category_id', $storeProductData2)->where('category_status', 1)->get()->pluck('category_name')->toArray();
                            $catString2 = implode(', ', @$catData2);
                            if (isset($catString2))
                                $string2 = substr(@$catString2, 0, 27);
                            else
                                $string2 = null;

                            $otherStores->categories =  @$string2;
                            // $otherStores->rating = number_format((float)4.20, 1, '.', '');
                            // $otherStores->ratingCount = 120;

                            $otherStores->rating = Helper::storeRating($otherStores->store_id);
                            $otherStores->ratingCount = Helper::storeRatingCount($otherStores->store_id);
                            $otherStoresTwo[] = $otherStores;
                        }
                    }
                    $data['otherStores']  = $otherStoresTwo;

                    $data['message'] = 'success';
                    $data['status'] = 1;
                } else {
                    if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                        $business_type_id = $request->business_type_id;

                        $data['sliderImages'] =  Mst_CustomerAppBanner::select('banner_id', 'image', 'town_id', 'status', 'default_status')->where('default_status', 1)->where('store_id', 0)->where('status', 1)->get();
                        foreach ($data['sliderImages'] as $img) {
                            $img->image = '/assets/uploads/customer_banner/' . $img->image;
                        }


                        // $data['categoriesList'] = Mst_categories::select('category_id','business_type_id','category_description','category_icon','category_name')->where('business_type_id', $business_type_id)->where('category_status', 1)->orderBy('category_id', 'DESC')->get();
                        // foreach($data['categoriesList'] as $cat)
                        // {
                        //   $cat->category_icon = '/assets/uploads/business_type/icons/'.$cat->category_icon;
                        // }


                        $productData = Mst_store_product::join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id');


                        if ((isset($request->customer_id)) && ($request->customer_id != 0)) {
                            // near by store
                            $cusData = Trn_store_customer::select('latitude', 'longitude')->where('customer_id', '=', $request->customer_id)->first();
                            // dd($cusData);

                            if (isset($request->latitude) && ($request->longitude)) {
                                $latitude = $request->latitude;
                                $longitude = $request->longitude;
                            } else {
                                $cusAddData = Trn_customerAddress::where('customer_id', '=', $request->customer_id)->where('default_status', 1)->first();

                                if (isset($cusAddData)) {
                                    $cusAddDataLat =  $cusAddData->latitude;
                                    $cusAddDataLog =  $cusAddData->longitude;
                                }

                                $latitude = $cusAddDataLat;
                                $longitude = $cusAddDataLog;
                            }
                            $productData = $productData->select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                            * cos(radians(mst_stores.latitude)) * cos(radians(mst_stores.longitude) - radians(" . $longitude . "))
                                            + sin(radians(" . $latitude . ")) * sin(radians(mst_stores.latitude))) AS distance"));
                            $productData = $productData->orderBy('distance');
                        }

                        $productData = $productData->where('mst_store_products.product_status', 1)
                            ->where('mst_store_products.show_in_home_screen', 1)
                            ->where('mst_stores.business_type_id', $business_type_id)
                            ->get();

                        $productDataFinal = array();
                        $stockCount = 0;
                        foreach ($productData as $offerProduct) {

                            if (Helper::productStock($offerProduct->product_id) > 0) {
                                $offerProduct->product_base_image = '/assets/uploads/products/base_product/base_image/' . $offerProduct->product_base_image;
                                $storeData = Mst_store::find($offerProduct->store_id);
                                $offerProduct->store_name = $storeData->store_name;
                                $offerProduct->rating = Helper::productRating($offerProduct->product_id);
                                $offerProduct->ratingCount = Helper::productRatingCount($offerProduct->product_id);

                                $offerProduct->productStock = Helper::productStock($offerProduct->product_id);
                                $offerProduct->variantCount = Helper::variantCount($offerProduct->product_id);
                                $offerProduct->isBaseVariant = Helper::isBaseVariant($offerProduct->product_id);
                                $offerProduct->attrCount = Helper::attrCount($offerProduct->product_id);

                                $productDataFinal[] =   $offerProduct;
                            }
                        }
                        $data['offerProducts']  =    $productDataFinal;




                        $recentlyVisited  = Trn_RecentlyVisitedStore::join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_stores.store_id')
                            ->join('trn__store_admins', 'trn__store_admins.store_id', '=', 'trn__recently_visited_stores.store_id')
                            ->where('trn__store_admins.role_id', 0)->where('mst_stores.online_status', 1)
                            ->where('trn__store_admins.store_account_status', 1)
                            ->where('mst_stores.business_type_id', $business_type_id)
                            ->where('trn__recently_visited_stores.customer_id', $request->customer_id)
                            ->orderBy('trn__recently_visited_stores.rvs_id', 'DESC')
                            //->groupBy('trn__recently_visited_stores.store_id')
                            ->limit(10)->get();

                        $recentlyVisited = collect($recentlyVisited);
                        $recentlyVisitedS = $recentlyVisited->unique('store_id');
                        $dataReViStore =   $recentlyVisitedS->values()->all();


                        $recentlyVisitedStores = $dataReViStore;

                        $recentStoreArray[] = 0;
                        $recentlyVisStrs = array();

                        foreach ($recentlyVisitedStores as $recentlyVisitedStore) {
                            //   $toDay = Carbon::now()->format('l');
                            //   $thisTime = Carbon::now()->format('H:i');

                            if (isset($recentlyVisitedStore->store_district_id))
                                $recentlyVisitedStore->district_name = District::find($recentlyVisitedStore->store_district_id)->district_name;
                            else
                                $recentlyVisitedStore->district_name = '';


                            $timeslotdata = Helper::findHoliday($recentlyVisitedStore->store_id);

                            if ($timeslotdata == true) {
                                $recentlyVisitedStore->thisTime = $timeslotdata;
                                $recentStoreArray[] = $recentlyVisitedStore->store_id;
                                if (isset($recentlyVisitedStore->profile_image)) {
                                    $recentlyVisitedStore->store_image =  '/assets/uploads/store_images/images/' . $recentlyVisitedStore->profile_image;
                                } else {
                                    $recentlyVisitedStore->store_image =  Helper::default_store_image();
                                }


                                $storeProductData = Mst_store_product::select('product_cat_id')->where('store_id', '=', $recentlyVisitedStore->store_id)->orderBy('product_id', 'DESC')->get()->unique('product_cat_id')->pluck('product_cat_id')->toArray();
                                $catData = Mst_categories::whereIn('category_id', $storeProductData)->where('category_status', 1)->get()->pluck('category_name')->toArray();
                                $catString = implode(', ', @$catData);
                                if (isset($catString))
                                    $string = substr(@$catString, 0, 27);
                                else
                                    $string = null;

                                $recentlyVisitedStore->categories =  @$string;
                                // $recentlyVisitedStore->rating = number_format((float)4.20, 1, '.', '');
                                // $recentlyVisitedStore->ratingCount = 120;

                                $recentlyVisitedStore->rating = Helper::storeRating($recentlyVisitedStore->store_id);
                                $recentlyVisitedStore->ratingCount = Helper::storeRatingCount($recentlyVisitedStore->store_id);
                                $recentlyVisStrs[] =    $recentlyVisitedStore;
                            }
                        }


                        $data['recentlyVisitedStores']   = $recentlyVisStrs;





                        if (isset($request->latitude) && ($request->longitude)) {
                            $latitude = $request->latitude;
                            $longitude = $request->longitude;
                        } else {
                            $cusData = Trn_store_customer::find($request->customer_id);
                            $cusAddData = Trn_customerAddress::where('customer_id', '=', $request->customer_id)->where('default_status', 1)->first();

                            if (isset($cusAddData)) {
                                $cusAddDataLat =  $cusAddData->latitude;
                                $cusAddDataLog =  $cusAddData->longitude;
                            } else {
                                $cusAddDataLat =  $cusAddDataLat;
                                $cusAddDataLog =  $cusAddDataLog;
                            }
                            $latitude = $cusAddDataLat;
                            $longitude = $cusAddDataLog;
                        }

                        if (isset($latitude) && isset($longitude)) {
                            $stores          =       DB::table("mst_stores")->join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id');
                            $stores         = $stores->where('trn__store_admins.role_id', 0);
                            $stores         = $stores->where('mst_stores.online_status', 1);
                            $stores         = $stores->where('mst_stores.business_type_id', $business_type_id);
                            $stores         = $stores->where('trn__store_admins.store_account_status', 1);
                            $stores          =       $stores->select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                                * cos(radians(mst_stores.latitude)) * cos(radians(mst_stores.longitude) - radians(" . $longitude . "))
                                                + sin(radians(" . $latitude . ")) * sin(radians(mst_stores.latitude))) AS distance"));
                            $stores          =       $stores->having('distance', '<', 10);
                            $stores          =       $stores->orderBy('distance', 'asc');
                            $nearByStoresData = $stores->get();
                        } else {
                            $nearByStoresData  = Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
                                ->where('trn__store_admins.role_id', 0)->where('mst_stores.online_status', 1)
                                ->where('mst_stores.business_type_id', $business_type_id)
                                ->where('trn__store_admins.store_account_status', 1)->orderBy('mst_stores.store_id', 'ASC')->limit(3)->get();
                        }

                        $nearStoreArray[] = 0;
                        $nearByStoreFinal = array();
                        foreach ($nearByStoresData as $nearByStore) {

                            $timeslotdata = Helper::findHoliday($nearByStore->store_id);

                            if ($timeslotdata == true) {

                                $nearStoreArray[] = $nearByStore->store_id;

                                if (isset($nearByStore->profile_image)) {
                                    $nearByStore->store_image =  '/assets/uploads/store_images/images/' . $nearByStore->profile_image;
                                } else {
                                    $nearByStore->store_image =  Helper::default_store_image();
                                }

                                if (isset($nearByStore->store_district_id))
                                    $nearByStore->district_name = District::find($nearByStore->store_district_id)->district_name;
                                else
                                    $nearByStore->district_name = '';


                                $storeProductData1 = Mst_store_product::select('product_cat_id')->where('store_id', '=', $nearByStore->store_id)->orderBy('product_id', 'DESC')->get()->unique('product_cat_id')->pluck('product_cat_id')->toArray();
                                $catData1 = Mst_categories::whereIn('category_id', $storeProductData1)->where('category_status', 1)->get()->pluck('category_name')->toArray();
                                $catString1 = implode(', ', @$catData1);
                                if (isset($catString1))
                                    $string1 = substr(@$catString1, 0, 27);
                                else
                                    $string1 = null;

                                $nearByStore->categories =  @$string1;


                                // $nearByStore->rating = number_format((float)4.20, 1, '.', '');
                                // $nearByStore->ratingCount = 120;

                                $nearByStore->rating = Helper::storeRating($nearByStore->store_id);
                                $nearByStore->ratingCount = Helper::storeRatingCount($nearByStore->store_id);
                                $nearByStoreFinal[] =  $nearByStore;
                            }
                        }

                        // other stores
                        // dd($nearStoreArray);

                        $data['nearByStores'] = $nearByStoreFinal;


                        $otherStores =  Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
                            ->where('trn__store_admins.role_id', 0)->where('mst_stores.online_status', 1)
                            ->where('mst_stores.business_type_id', $business_type_id)
                            ->where('trn__store_admins.store_account_status', 1)
                            ->whereNotIn('mst_stores.store_id', $nearStoreArray)
                            ->whereNotIn('mst_stores.store_id', $recentStoreArray);


                        if (isset($latitude) && ($longitude)) {
                            $otherStores = $otherStores->select("*", DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                                * cos(radians(mst_stores.latitude)) * cos(radians(mst_stores.longitude) - radians(" . $longitude . "))
                                                + sin(radians(" . $latitude . ")) * sin(radians(mst_stores.latitude))) AS distance"));
                            $otherStores =   $otherStores->orderBy('distance');
                        }

                        $otherStores = $otherStores->get();

                        $otherStoresFinal = array();

                        foreach ($otherStores as $otherStores) {

                            $timeslotdata = Helper::findHoliday($otherStores->store_id);

                            if ($timeslotdata == true) {

                                if (isset($otherStores->profile_image)) {
                                    $otherStores->store_image =  '/assets/uploads/store_images/images/' . $otherStores->profile_image;
                                } else {
                                    $otherStores->store_image =  Helper::default_store_image();
                                }
                                if (isset($otherStores->store_district_id))
                                    $otherStores->district_name = District::find($otherStores->store_district_id)->district_name;
                                else
                                    $otherStores->district_name = '';

                                $storeProductData2 = Mst_store_product::select('product_cat_id')->where('store_id', '=', $otherStores->store_id)->orderBy('product_id', 'DESC')->get()->unique('product_cat_id')->pluck('product_cat_id')->toArray();
                                $catData2 = Mst_categories::whereIn('category_id', $storeProductData2)->where('category_status', 1)->get()->pluck('category_name')->toArray();
                                $catString2 = implode(', ', @$catData2);
                                if (isset($catString2))
                                    $string2 = substr(@$catString2, 0, 27);
                                else
                                    $string2 = null;

                                $otherStores->categories =  @$string2;


                                // $otherStores->rating = number_format((float)4.20, 1, '.', '');
                                // $otherStores->ratingCount = 120;

                                $otherStores->rating = Helper::storeRating($otherStores->store_id);
                                $otherStores->ratingCount = Helper::storeRatingCount($otherStores->store_id);
                                $otherStoresFinal[] = $otherStores;
                            }
                        }

                        $data['otherStores'] = $otherStoresFinal;
                        $data['message'] = 'success';
                        $data['status'] = 1;
                    } else {
                        $data['message'] = 'Customer not found';
                        $data['status'] = 0;
                    }
                }
            } else {
                $data['message'] = 'Business type not found';
                $data['status'] = 0;
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


    public function typeList(Request $request)
    {
        $data = array();
        try {

            $data['Business_Types'] = Mst_business_types::select('business_type_id', 'business_type_name', 'business_type_icon')->where('business_type_status', 1)->orderBy('business_type_name', 'ASC')->get();
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];

            return response($response);
        }
    }

    public function OfferProducts(Request $request)
    {
        $data = array();
        try {
            if (isset($request->business_type_id) && Mst_business_types::find($request->business_type_id)) {
                $business_type_id = $request->business_type_id;

                if (
                    $data['offerProducts']  = Mst_store_product::join('mst_store_product_varients', 'mst_store_product_varients.product_id', '=', 'mst_store_products.product_id')
                    ->join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id')
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
                    ->where('mst_stores.business_type_id', $business_type_id)
                    ->where('mst_store_products.product_status', 1)
                    ->where('mst_store_products.show_in_home_screen', 1)->get()
                ) {

                    foreach ($data['offerProducts'] as $product) {
                        $product->product_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_base_image;
                        $product->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_varient_base_image;
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
                $data['message'] = "Business type not found ";
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


    public function RecentlyVisited(Request $request)
    {
        $data = array();
        try {
            if (isset($request->business_type_id) && Mst_business_types::find($request->business_type_id)) {
                if (isset($request->customer_id) && Trn_store_customer::find($request->customer_id)) {
                    $business_type_id = $request->business_type_id;
                    $customer_id = $request->customer_id;

                    if (
                        $data['recentlyVisitedProducts']  = Trn_RecentlyVisitedProducts::join('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
                        ->join('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
                        ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
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
                        ->where('trn__recently_visited_products.customer_id', $customer_id)
                        ->where('mst_stores.business_type_id', $business_type_id)
                        ->where('mst_store_products.product_status', 1)
                        ->take(3)->get()
                    ) {

                        foreach ($data['recentlyVisitedProducts'] as $product) {
                            $product->product_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_base_image;
                            $product->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_varient_base_image;
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
            } else {
                $data['status'] = 0;
                $data['message'] = "Business type not found ";
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


    public function storeList(Request $request)
    {
        $data = array();
        try {
            if (isset($request->business_type_id) && Mst_business_types::find($request->business_type_id)) {
                $business_type_id = $request->business_type_id;

                if (
                    $data['storeList']  = Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
                    ->where('mst_stores.business_type_id', $business_type_id)
                    ->where('trn__store_admins.store_account_status', 1)
                    ->where('trn__store_admins.role_id', 0)
                    ->take(5)->get()
                ) {

                    foreach ($data['storeList'] as $store) {
                        $store->storeImage = Mst_store_images::where('store_id', $store->store_id)->get();
                        foreach ($store->storeImage as $s) {
                            @$s->store_image = '/assets/uploads/store_images/images/' . @$s->store_image;
                        }
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
                $data['message'] = "Business type not found ";
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
