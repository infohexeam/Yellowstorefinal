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
use App\Models\admin\Mst_StockDetail;

class InventoryController extends Controller
{


    public function listInventoryProducts(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                $store_id = $request->store_id;

                $validator = Validator::make(
                    $request->all(),
                    [
                        'category_id'          => 'required',
                    ],
                    [
                        'category_id.required'        => 'Category required',
                    ]
                );

                if (!$validator->fails()) {
                    if ($request->category_id == 0  ||   Mst_categories::find($request->category_id)) {
                       
                        if ($request->category_id == 0) {
                            if ($inventoryData = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                            ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                            ->where('mst_store_products.store_id', $request->store_id)
                            ->where('mst_store_products.is_removed', 0)
                            ->where('mst_store_categories.category_status', 1)
                            ->where('mst_store_product_varients.is_removed', 0)
                            ->where('mst_store_products.product_type', 1)
                            ->where('mst_store_products.is_product_listed_by_product',0)
                            ->where('mst_store_product_varients.variant_name', 'LIKE', '%' . $request->product_name . '%')
                            ->select(
                                'mst_store_products.product_id',
                                'mst_store_products.product_name',
                                'mst_store_products.product_code',
                                'mst_store_products.product_cat_id',
                                'mst_store_products.product_base_image',
                                'mst_store_products.product_status',
                                'mst_store_products.product_brand',
                                'mst_store_products.tax_id',
                                'mst_store_product_varients.product_varient_id',
                                'mst_store_product_varients.variant_name',
                                'mst_store_product_varients.product_varient_price',
                                'mst_store_product_varients.product_varient_offer_price',
                                'mst_store_product_varients.product_varient_base_image',
                                'mst_store_product_varients.stock_count',
                                'mst_store_products.stock_count as minimum_stock',
                                'mst_store_product_varients.included_in_low_stock_alert',
                                'mst_store_categories.category_id',
                                'mst_store_categories.category_name'
                            )
                            ->orderByRaw('mst_store_product_varients.included_in_low_stock_alert DESC,CASE WHEN mst_store_product_varients.stock_count <= mst_store_products.stock_count THEN 0 ELSE 1 END')
                            ->orderBy('mst_store_product_varients.stock_count', 'ASC')
                            ->get()
                        
                            ) {    
                                foreach ($inventoryData as $product) {
                                    $product->less_or_equal_min_stock=0;
                                    if($product->stock_count<=$product->minimum_stock)
                                    {
                                        $product->less_or_equal_min_stock=1;

                                    }
                                    $product->product_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_base_image;
                                    $product->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_varient_base_image;
                                }
                $inventoryDatassss = collect($inventoryData);
                $perPage = 15;
                $page=$request->page??1;
                $offset = ($page - 1) * $perPage;
                $roWc=count($inventoryDatassss);
                $dataReViStoreSS =   $inventoryDatassss->slice($offset, $perPage)->values()->all();
                $data['productDetails']=$dataReViStoreSS;
                if ($roWc >14) {
                    $data['pageCount'] = ceil(@$roWc /15);
                 } else {
                     $data['pageCount'] = 1;
                 }
                $data['status'] = 1;
                $data['currentPage']=$page;
                                $data['message'] = "success";
                                return response($data);
                            } else {
                                $data['status'] = 0;
                                $data['message'] = "failed";
                                return response($data);
                            }

                        } else {
                            
                            if ($query  = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
                                ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
                                ->where('mst_store_products.product_type', 1)
                                ->where('mst_store_products.is_product_listed_by_product',0)
                                ->where('mst_store_products.is_removed', 0)
                                ->where('mst_store_categories.category_status', 1)
                                ->where('mst_store_product_varients.is_removed', 0)
                                ->where('mst_store_product_varients.store_id',$request->store_id)
                                
                                ->select(
                                    'mst_store_products.product_id',
                                    'mst_store_products.product_name',
                                    'mst_store_products.product_code',
                                    'mst_store_products.product_cat_id',
                                    //'mst_store_products.tax_id',
                                    'mst_store_products.stock_count as minimum_stock',
                                    'mst_store_products.product_base_image',
                                    'mst_store_products.product_status',
                                    'mst_store_products.product_brand',
                                    'mst_store_product_varients.product_varient_id',
                                    'mst_store_product_varients.variant_name',
                                    'mst_store_product_varients.product_varient_price',
                                    'mst_store_product_varients.product_varient_offer_price',
                                    'mst_store_product_varients.product_varient_base_image',
                                    'mst_store_product_varients.stock_count',
                                    'mst_store_product_varients.included_in_low_stock_alert',
                                    'mst_store_categories.category_id',
                                    'mst_store_categories.category_name'
                                )
                            ) {

                                


                                if (isset($request->category_id)) {
                                    $query = $query->where('mst_store_products.product_cat_id', $request->category_id);
                                }

                                if (isset($request->product_name)) {
                                    $query = $query->where('mst_store_product_varients.variant_name', 'LIKE', '%' . $request->product_name . '%');
                                    //$query = $query->orWhere('mst_store_product_varients.variant_name', 'LIKE', "%{$request->product_name}%");
                                    //->orWhere('mst_store_product_varients.variant_name', 'LIKE', "%{$request->product_name}%");



                                }






                                $data['productDetails'] = $query->orderByRaw('mst_store_product_varients.included_in_low_stock_alert DESC,CASE WHEN mst_store_product_varients.stock_count <= mst_store_products.stock_count THEN 0 ELSE 1 END')
                                ->orderBy('mst_store_product_varients.stock_count', 'ASC')->get();

                                foreach ($data['productDetails'] as $product) {
                                    $product->product_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_base_image;
                                    $product->product_varient_base_image = '/assets/uploads/products/base_product/base_image/' . $product->product_varient_base_image;
                                }
                                $inventoryDatassss = collect($data['productDetails']);
                                $perPage = 15;
                                $page=$request->page??1;
                                $offset = ($page - 1) * $perPage;
                                $roWc=count($inventoryDatassss);
                                $dataReViStoreSS =   $inventoryDatassss->slice($offset, $perPage)->values()->all();
                                $data['productDetails']=$dataReViStoreSS;
                                if ($roWc >14) {
                                    $data['pageCount'] = ceil(@$roWc /15);
                                 } else {
                                     $data['pageCount'] = 1;
                                 }
                                $data['status'] = 1;
                                $data['currentPage']=$page;
                                $data['message'] = "success";
                                return response($data);
                            } else {
                                $data['status'] = 0;
                                $data['message'] = "failed";
                                return response($data);
                            }
                        }
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "failed";
                        $data['message'] = "Category not found ";
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "failed";
                    $data['message'] = "Category not found ";
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

    public function updateInventory(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                if (isset($request->product_varient_id) && Mst_store_product_varient::find($request->product_varient_id)) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'stock_count'   => 'required',
                            'stock_count'   => 'numeric',
                        ],
                        [
                            'stock_count.required'  => 'Stock count required',
                            'stock_count.numeric'   => 'Stock count must be a numeric',
                        ]
                    );

                    if (!$validator->fails()) {
                        $proVar = Mst_store_product_varient::find($request->product_varient_id);
                        $productData['stock_count'] = $request->stock_count +  $proVar->stock_count; // stock count
                         if($productData['stock_count']<0)
                         {
                            $data['status'] = 0;
                            $data['message'] = "Negative Stock update not allowed.";
                            return response($data);

                         }

                        if (Mst_store_product_varient::where('product_varient_id', $request->product_varient_id)->update($productData)) {

                            $usData = DB::table('mst_store_product_varients')->where('product_varient_id', $request->product_varient_id)->first();
                            $usProData =  DB::table('mst_store_products')->where('product_id', $usData->product_id)->first();

                            //$productData2['product_status'] = 1;
                            //Mst_store_product::where('product_id', $usData->product_id)->update($productData2);

                            $sd = new Mst_StockDetail;
                            $sd->store_id = $usProData->store_id;
                            $sd->product_id = $usData->product_id;
                            $sd->stock = $request->stock_count;
                            $sd->product_varient_id = $request->product_varient_id;
                            $sd->prev_stock = $proVar->stock_count;
                            $sd->save();



                            $data['status'] = 1;
                            $data['message'] = "Stock updated successfully.";
                            return response($data);
                        } else {
                            $data['status'] = 0;
                            $data['message'] = "Stock updates failed.";
                            return response($data);
                        }
                    } else {
                        $data['status'] = 0;
                        $data['message'] = $validator->errors();
                        // $data['message'] = "failed";
                        // $data['errors'] = $validator->errors();
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Product not found ";
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
        }
    }


    public function resetStock(Request $request)
    {
        $data = array();
        try {
            if (isset($request->store_id) && Mst_store::find($request->store_id)) {
                if (isset($request->product_varient_id) && Mst_store_product_varient::find($request->product_varient_id)) {
                    $productData['stock_count'] = 0;

                    if (Mst_store_product_varient::where('product_varient_id', $request->product_varient_id)->update($productData)) {
                        //$data['productstock'] = $productData->stock_count;
                     // return 999;
                        $usData = DB::table('mst_store_product_varients')->where('product_varient_id', $request->product_varient_id)->first();
                        // if($usData)
                        // {
                        //     $usData->stock_count=999;
                        //     $usData->update();
                        // }
                        
                        $usProDataSum = DB::table('mst_store_product_varients')->where('product_id', $usData->product_id)->sum('stock_count');
                        
                        if($usProDataSum <= 0){
                            //$productData2['product_status'] = 0;
                           // Mst_store_product::where('product_id', $usData->product_id)->update($productData2);
                        }
                        

                        // $dataPro = Mst_store_product::where('product_id', $usData->product_id);
                       /* $sd = new Mst_StockDetail;
                        $sd->store_id = $request->store_id;
                        $sd->product_id = $usData->product_id;
                        $sd->stock = ($usData->stock_count * -1);
                        $sd->product_varient_id = $request->product_varient_id;
                        $sd->prev_stock = $usData->stock_count??0;
                        $sd->save();*/
                        Db::table('empty_stock_log')->where('product_varient_id',$request->product_varient_id)->delete();
                        DB::table('empty_stock_log')->insert(['product_varient_id'=>$request->product_varient_id,'created_time' => Carbon::now()]);

                        $data['status'] = 1;
                        $data['message'] = "Stock reset successfully.";
                        return response($data);
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "Failed";
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Product not found ";
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
        }
    }
    public function updateInclusionstatus(Request $request)
    {
     $data=array();
      try {    
        $pvarient=Mst_store_product_varient::find($request->product_varient_id);
        if($pvarient->included_in_low_stock_alert==0)
        {
          $status=1;
  
        }
        else
        {
          $status=0;
        }
        Mst_store_product_varient::where('product_varient_id', $request->product_varient_id)->update(['included_in_low_stock_alert' => $status]);
        $data['status']=1;
        $data['message']="Status updated successfully";
        return response($data);
      } catch (\Exception $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    } catch (\Throwable $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
    }
    }
    
}
